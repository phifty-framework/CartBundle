<?php
namespace CartBundle;

use CartBundle\Exception\CartException;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
use CartBundle\Model\Coupon;
use CartBundle\Model\Logistics;
use CartBundle\ShippingFeeRule\ShippingFeeRule;
use CartBundle\ShippingFeeRule\NoShippingFeeRule;
use CartBundle\ShippingFeeRule\DefaultShippingFeeRule;
use CartBundle\CartStorage\CartStorage;
use CartBundle\CartStorage\SessionCartStorage;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use LazyRecord\BaseCollection;
use LogicException;
use ArrayIterator;
use IteratorAggregate;
use Countable;

/**
 * Contains the logics of Cart.
 *
 * + Order item total amount
 * + Shipping cost
 * = Total Amount
 *   - discount amount
 *   = discounted total amount
 */
class Cart implements IteratorAggregate, Countable
{
    /**
     * Storage for saving order items for users.
     */
    public $storage;

    protected $logistics;

    protected $bundle;

    protected $coupon;

    /* when we want to support multiple coupon?
    protected $coupons = [];
     */

    protected $shippingFeeRule;

    public function __construct(CartStorage $storage)
    {
        $this->storage = $storage;

        // Right now we need bundle to get some configs:
        // - UseProductTypeQuantity
        // - NoShippingFeeCondition.AboveAmount
        $this->bundle = CartBundle::getInstance();
        $this->removeInvalidItems($this->bundle->config('UseProductTypeQuantity'), $this->bundle->config('UseProductTypeQuantity'));
        $this->shippingFeeRule = new DefaultShippingFeeRule($this->bundle);
    }

    public function containsProduct(Product $product) : bool
    {
        if ($collection = $this->storage->all()) {
            foreach ($collection as $item) {
                if ($item->product_id == $product->id) {
                    return true;
                }
            }
        }
        return false;
    }

    public function setShippingFeeRule(ShippingFeeRule $rule)
    {
        $this->shippingFeeRule = $rule;
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
        $totalAmount += $this->calculateShippingFee();

        return $totalAmount;
    }

    public function calculateDiscountAmount()
    {
        $discountedAmount = $this->calculateDiscountedTotalAmount();
        $totalAmount = $this->calculateTotalAmount();

        return $totalAmount - $discountedAmount;
    }

    public function getCurrentCoupon()
    {
        return $this->coupon;
    }

    public function calculateDiscountedTotalAmount()
    {
        $totalAmount = $this->calculateTotalAmount();
        if ($this->coupon) {
            return $this->coupon->calcualteDiscount($totalAmount);
        }
        return $totalAmount;
        /* for multiple coupons
        if (count($this->coupons) > 0) {
            return array_reduce($this->coupons, function($carry, $coupon) {
                return $coupon->calcualteDiscount($carry);
            }, $totalAmount);
        }
         */
    }

    /**
     * Coupon related logics.
     */
    public function applyCoupon(Coupon $coupon, & $reason = null)
    {
        // always validate coupon
        list($success, $reason) = $coupon->isValid($this);
        if ($success) {
            $this->coupon = $coupon;
            // $this->coupons[$coupon->coupon_code] = $coupon;
            return true;
        }

        return false;
    }

    public function usingCoupon()
    {
        return $this->coupon ? true : false;
        // return !empty($this->coupons);
    }

    public function cleanup()
    {
        $this->storage->removeAll();
    }

    public function calculateShippingFee()
    {
        if (!$this->shippingFeeRule) {
            throw new LogicException('Shipping Fee Rule is not given.');
        }
        return $this->shippingFeeRule->calculate($this);
    }

    /**
     * Return Cart Summary.
     */
    public function getSummary()
    {
        return array(
            'orderitem_total_amount' => $this->calculateOrderItemTotalAmount(),
            'shipping_fee' => $this->calculateShippingFee(),

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
        $itemsByProduct = [];
        if ($items = $this->storage->all()) {
            foreach ($items as $item) {
                $itemsByProduct[$item->product_id][ $item->type_id ?: 0 ][] = $item;
            }
        }
        $mergedItems = [];
        foreach ($itemsByProduct as $productId => $itemsByType) {
            foreach ($itemsByType as $typeId => $items) {
                if (count($items) <= 1) {
                    array_splice($mergedItems, 0, 0, $items);
                    continue;
                }
                // merge them
                $firstItem = $items[0];
                $quantity = array_reduce($items, function($carry, $subItem) {
                    return $carry + $subItem->quantity;
                }, 0);
                $firstItem->update([ 'quantity' => $quantity ]);
                $mergedItems[] = $firstItem;
            }
        }
        $this->storage->set($mergedItems);
        return $mergedItems;
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
        $instance = new static($storage ?: new SessionCartStorage);
        return $instance;
    }


    /**
     * @return OrderItemCollection return order items from storage.
     */
    public function getItems()
    {
        return $this->storage->all();
    }

    /**
     * Return order item collection the storage.
     */
    public function getIterator()
    {
        return $this->storage->all();
    }

    public function count()
    {
        return count($this->storage);
    }

}
