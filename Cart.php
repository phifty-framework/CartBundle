<?php
namespace CartBundle;

use CartBundle\Exception\CartException;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
use CartBundle\CartStorage\CartStorage;
use CartBundle\CartStorage\SessionCartStorage;
use CouponBundle\Model\Coupon;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use ShippingBundle\Model\Company as ShippingCompany;
use LazyRecord\BaseCollection;
use ArrayIterator;
use IteratorAggregate;

/**
 * Contains the logics of Cart.
 *
 * + Order item total amount
 * + Shipping cost
 * = Total Amount
 *   - discount amount
 *   = discounted total amount
 */
class Cart implements IteratorAggregate
{
    /**
     * Storage for saving order items for users.
     */
    public $storage;

    public $shippingCompany = 'default';

    public function __construct(CartStorage $storage)
    {
        $this->storage = $storage;

        $bundle = kernel()->bundle('CartBundle');
        $this->removeInvalidItems($bundle->config('UseProductTypeQuantity'), $bundle->config('UseProductTypeQuantity'));
    }

    public function containsProduct(Product $product) : bool
    {
        if ($collection = $this->storage->all()) {
            foreach ($collection as $item) {
                if (in_array($item->product_id, $product->id)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if the current order item contains the any of the given product Id
     */
    public function containsProducts(array $productIds) : bool
    {
        if ($collection = $this->storage->all()) {
            foreach ($collection as $orderItem) {
                if (in_array($orderItem->product_id, $productIds)) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Create new order item based on the given product id and type 
     *
     * @param integer $productId
     * @param integer $typeId
     * @param integer $quantity
     *
     * @return OrderItem
     */
    public function addProduct(Product $product, ProductType $givenType = null, $quantity = 1)
    {
        if (!$product->id) {
            throw new CartException(_('找不到商品'));
        }

        $foundType = null;
        if ($givenType) {
            foreach ($product->types as $type) {
                if (intval($type->id) === intval($givenType->id)) {
                    $foundType = $givenType;
                    break;
                }
            }
            if (!$foundType) {
                throw new CartException(_('此產品無此類型'));
            }
            // Validate quantity for the specific type
            if ($foundType->quantity < $quantity) {
                // XXX: warning...
            }
        }


        // Always convert quantity into integer
        $quantity = intval($quantity);

        // find the same product and type,
        // if it's the same, we should simply update the quantity instead of creating new items
        if ($items = $this->storage->all()) {
            foreach ($items as $item) {
                if (intval($item->product_id) != intval($product->id)) {
                    continue;
                }
                if ($givenType && intval($item->type_id) != intval($givenType->id)) {
                    continue;
                }
                // Update the existing order item
                $item->update([ 'quantity' => intval($item->quantity) + $quantity ]);
                return $item;
            }
        }
        return $this->newItem($product, $givenType, $quantity);
    }

    /**
     * Calcualte the total quantity from the current order items.
     *
     * @return int
     */
    public function calculateTotalQuantity()
    {
        if ($collection = $this->storage->all()) {
            return $collection->calculateTotalQuantity();
        }

        return 0;
    }

    /**
     * Return the sum of amount from all order items, this method does not count shipping cost in.
     *
     * @return int The total amount
     */
    public function calculateOrderItemTotalAmount()
    {
        if ($collection = $this->storage->all()) {
            return $collection->calculateTotalAmount();
        }

        return 0;
    }

    public function calculateTotalAmount()
    {
        $totalAmount = 0;
        $totalAmount += $this->calculateOrderItemTotalAmount();
        $totalAmount += $this->calculateShippingCost();

        return $totalAmount;
    }

    public function calculateDiscountAmount()
    {
        $discountedAmount = $this->calculateDiscountedTotalAmount();
        $totalAmount = $this->calculateTotalAmount();

        return $totalAmount - $discountedAmount;
    }

    public function calculateDiscountedTotalAmount()
    {
        $totalAmount = $this->calculateTotalAmount();
        if ($coupon = $this->loadSessionCoupon()) {
            return $coupon->calcualteDiscount($totalAmount);
        }

        return $totalAmount;
    }

    /**
     * Coupon related logics.
     */
    public function applyCoupon($coupon)
    {
        // always validate coupon
        list($success, $reason) = $coupon->isValid($this);
        if ($success) {
            $_SESSION['coupon_code'] = $coupon->coupon_code;
            return true;
        }

        return false;
    }

    public function usingCoupon()
    {
        // the session is registered only when the coupon is validated..
        return isset($_SESSION['coupon_code']);
    }

    /**
     * check current coupon and re-validate the coupon.
     */
    public function loadSessionCoupon()
    {
        if (isset($_SESSION['coupon_code'])) {
            $coupon = new Coupon(['coupon_code' => $_SESSION['coupon_code']]);
            // always validate coupon
            list($success, $reason) = $coupon->isValid($this);
            if ($success) {
                return $coupon;
            }
            // if it's invalid coupon, just delete the sesssion
            unset($_SESSION['coupon_code']);
        }
    }

    public function cleanUp()
    {
        unset($_SESSION['coupon_code']);
        unset($_SESSION['items']);
        $this->storage->removeAll();
    }

    public function calculateShippingCost()
    {
        $bundle = kernel()->bundle('CartBundle');
        if ($aboveAmount = $bundle->config('NoShippingFeeCondition.AboveAmount')) {
            $orderItemAmount = $this->calculateOrderItemTotalAmount();
            if ($orderItemAmount >= $aboveAmount) {
                return 0;
            }
        }

        // Load default shipping method
        $company = new ShippingCompany(['handle' => $this->shippingCompany]);
        if ($company->id && $this->storage->all()) {
            return $company->shipping_cost;
        }
        return 0;
    }

    /**
     * Return Cart Summary.
     */
    public function getSummary()
    {
        return array(
            'orderitem_total_amount' => $this->calculateOrderItemTotalAmount(),
            'shipping_cost' => $this->calculateShippingCost(),

            // the original total amount (including shipping cost)
            'total_amount' => $this->calculateTotalAmount(),

            // discounted total amount
            'discounted_total_amount' => $this->calculateDiscountedTotalAmount(),

            // discount amount (from coupon)
            'discount_amount' => $this->calculateDiscountAmount(),
        );
    }

    /**
     * This method is used internally.
     *
     * @param OrderItem $item
     */
    protected function validateItemQuantity(OrderItem $item)
    {
        $t = $item->type;
        if (!$t || !$t->id || ($t->quantity !== null && $item->quantity > $t->quantity)) {
            return false;
        }
        return true;
    }


    /**
     * This method validates record's existence and the related product id, product type id.
     *
     * @return boolean
     */
    public function validateItem(OrderItem $item, $validateType = false, $validateQuantity = false)
    {
        if (!$item->id) {
            return false;
        }
        if ($item->order_id) {
            return false;
        }
        $p = $item->product;
        if (!$p || !$p->id) {
            return false;
        }
        if ($validateType) {
            $t = $item->type;
            if (!$t || !$t->id) {
                return false;
            }
            if ($validateQuantity && $t->quantity !== null && $item->quantity > $t->quantity) {
                return false;
            }
        }
        return true;
    }



    /**
     * Merge items merge order items that shares the same product and product type
     */
    public function mergeItems()
    {
        if ($items = $this->storage->all()) {
            foreach ($items as $item) {

            }
        }
    }


    /**
     * Remove invalid order items stored in the storage.
     *
     * @return OrderItem[] Invalid order items will be returned.
     */
    public function removeInvalidItems($validateType = false, $validateQuantity = false) : array
    {
        $invalidItems = [];
        // using session as our storage
        if ($collection = $this->storage->all()) {
            $self = $this;
            $items = array_filter($collection->items(), function($item) use ($self, & $invalidItems, $validateType, $validateQuantity) {
                if (false === $self->validateItem($item, $validateType, $validateQuantity)) {
                    $invalidItems[] = $item;
                    return false;
                }
                return true;
            });
            $this->storage->set($items);
        }
        return $invalidItems;
    }




    /**
     * Add an order item to the storage
     */
    public function addItem(OrderItem $item)
    {
        if (!$this->storage->contains($item)) {
            $this->storage->add($item);
        }
    }


    /**
     * Delete an order item from the storage and the database.
     */
    public function deleteItem(OrderItem $item)
    {
        // Order item with order_id can't be deleted.
        if ($item->order_id) {
            return false;
        }
        $ret = $item->delete();
        if ($ret->error) {
            throw new Exception("Can't remove item from cart.");
        }
        $this->storage->remove($item);
        return true;
    }

    /**
     * Create a new order item record and add it to the storage.
     *
     * @param Product     $product
     * @param ProductType $type
     * @param integer     $quantity
     * @return boolean
     */
    protected function newItem(Product $product, ProductType $type = null, $quantity) : OrderItem
    {
        $item = new OrderItem;
        $ret = $item->create([
            'product_id' => $product->id,
            'type_id'    => $type ? $type->id : null,
            'quantity'   => intval($quantity),
        ]);
        if (!$ret->success) {
            throw new CartException(_('無法新增至購物車'));
        }
        $this->storage->add($item);
        return $item;
    }





    /**
     * Update product type or quantity of an order item.
     *
     * @param int      $itemId
     * @param int      $typeId
     * @param interger $quantity
     *
     * @return true
     */
    public function updateItem(OrderItem $item, ProductType $type = null, $quantity)
    {
        /*
        $item = new OrderItem(intval($itemId));
        if (!$item->id) {
            throw new CartException(_('無此項目'));
        }
        */
        if ($item->order_id) {
            throw new CartException(_('不可更新已經下訂之訂單項目'));
        }
        $args = array();
        if ($type) {
            if ($product = $item->product) {
                foreach ($product->types as $productType) {
                    if (intval($productType->id) === intval($type->id)) {
                        $args['type_id'] = $type->id;
                    }
                }
            }
        }
        if ($quantity) {
            $args['quantity'] = intval($quantity);
        }
        if (empty($args)) {
            return false;
        }
        $ret = $item->update($args);
        if (!$ret->success) {
            throw new CartException(_('無法新增至購物車'));
        }
        return true;
    }


    public static function getInstance(CartStorage $storage = null)
    {
        static $instance;
        if ($instance) {
            return $instance;
        }
        return $instance = new static($storage ?: new SessionCartStorage);
    }


    /**
     * Return order item collection the storage.
     */
    public function getIterator()
    {
        return $this->storage->all();
    }

}
