<?php
namespace CartBundle;

use CartBundle\Exception\CartException;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
use CouponBundle\Model\Coupon;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use ShippingBundle\Model\Company as ShippingCompany;

/**
 * Contains the logics of Cart.
 *
 * + Order item total amount
 * + Shipping cost
 * = Total Amount
 *   - discount amount
 *   = discounted total amount
 */
class Cart
{
    /**
     * Storage for saving order items for users.
     */
    protected $storage;

    public $quantityInvalidItems = array();

    public $shippingCompany = 'default';

    public function __construct()
    {
        // TODO: provide options to specify storage engine.
        $this->storage = new SessionCartStorage();
        $this->validateItems();
    }

    public function containsProducts(array $productIds)
    {
        $orderItems = $this->getOrderItems();
        foreach ($orderItems as $orderItem) {
            if (in_array($orderItem->product_id, $productIds)) {
                return true;
            }
        }

        return false;
    }

    public function removeItem($id)
    {
        if ($this->deleteOrderItem($id)) {
            $this->storage->remove($id);

            return true;
        }

        return false;
    }

    public function addItem($productId, $typeId, $quantity = 1)
    {
        $product = new Product(intval($productId));
        if (!$product->id) {
            throw new CartException(_('找不到商品'));
        }

        $foundType = null;
        foreach ($product->types as $type) {
            if (intval($type->id) === intval($typeId)) {
                $foundType = $type;
                break;
            }
        }
        if (!$foundType) {
            throw new CartException(_('此產品無此類型'));
        }

        if ($foundType->quantity < $quantity) {
            // XXX: warning...
        }

        // Create the order item with session here....
        $quantity = intval($quantity);

        // find the same product and type, 
        // if it's the same, we should simply update the quantity instead of creating new items
        if ($items = $this->getOrderItems()) {
            foreach ($items as $item) {
                if ($item->product_id == $product->id && $item->type_id == $foundType->id) {
                    $item->update(array(
                        'quantity' => intval($item->quantity) + $quantity,
                    ));

                    return true;
                }
            }
        }

        $item = $this->createOrderItem($product, $foundType, $quantity);
        $this->storage->add($item->id);

        return true;
    }

    /**
     * Calcualte the total quantity from the current order items.
     *
     * @return int
     */
    public function calculateTotalQuantity()
    {
        if ($collection = $this->getOrderItems()) {
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
        if ($collection = $this->getOrderItems()) {
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
        if ($company->id && $this->getOrderItems()) {
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
     * Get item id list from storage and rebless them into objects.
     *
     * @return OrderItemCollection
     */
    public function getOrderItems()
    {
        $items = $this->storage->get();
        if (count($items)) {
            $collection = new OrderItemCollection();
            foreach ($items as $id) {
                $item = new OrderItem(intval($id));
                if ($item->id) {
                    $collection->add($item);
                }
            }
            return $collection;
        }
        return array();
    }

    public function validateItemQuantity($item)
    {
        $t = $item->type;
        if (!$t || !$t->id) {
            return false;
        }
        if ($item->quantity > $t->quantity) {
            return false;
        }

        return true;
    }

    public function validateItem($item)
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
        $t = $item->type;
        if (!$t || !$t->id) {
            return false;
        }

        return true;
    }

    public function isInvalidItem($item)
    {
        return in_array($item->id, $this->quantityInvalidItems);
    }

    public function purgeQuantityInvalidItems()
    {
        $bundle = kernel()->bundle('CartBundle');

        // using session as our storage
        $items = $this->storage->get();
        $this->quantityInvalidItems = array();
        if (count($items)) {
            $newItems = array();
            foreach ($items as $id) {
                $item = new OrderItem(intval($id));

                if (false == $this->validateItem($item)) {
                    continue;
                }
                if ($bundle->config('UseProductTypeQuantity') && false == $this->validateItemQuantity($item)) {
                    continue;
                }
                $newItems[] = intval($id);
            }
            $this->storage->set($newItems);
        }
    }

    public function validateItems()
    {
        $bundle = kernel()->bundle('CartBundle');

        // using session as our storage
        $items = $this->storage->get();
        $this->quantityInvalidItems = array();
        if (count($items)) {
            $newItems = array();
            foreach ($items as $id) {
                $item = new OrderItem(intval($id));
                if ($this->validateItem($item)) {
                    $newItems[] = intval($id);

                    if ($bundle->config('UseProductTypeQuantity')  && false === $this->validateItemQuantity($item)) {
                        $this->quantityInvalidItems[] = intval($id);
                    }
                }
            }
            $this->storage->set($newItems);
        }
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
    public function updateOrderItem($itemId, $typeId, $quantity)
    {
        $item = new OrderItem(intval($itemId));
        if (!$item->id) {
            throw new CartException(_('無此項目'));
        }
        if ($item->order_id) {
            throw new CartException(_('不可更新已經下訂之訂單項目'));
        }

        $args = array();
        if ($typeId) {
            $type = new ProductType(intval($typeId));
            if (!$type->id) {
                throw new CartException(_('無此產品類型'));
            }
            $args['type_id'] = $type->id;
        }
        if ($quantity) {
            $args['quantity'] = $quantity;
        }

        if (empty($args)) {
            return $item;
        }

        $ret = $item->update($args);
        if (!$ret->success) {
            throw new CartException(_('無法新增至購物車'));
        }

        return $item;
    }

    public function deleteOrderItem($id)
    {
        $item = new OrderItem(intval($id));
        // does not belongs to an order
        if ($item->order_id) {
            return false;
        }
        $ret = $item->delete();

        return $ret->success;
    }

    public function createOrderItem($product, $type, $quantity)
    {
        $item = new OrderItem();
        $ret = $item->create([
            'product_id' => $product->id,
            'type_id' => $type->id,
            'quantity' => $quantity,
        ]);
        if (!$ret->success) {
            throw new CartException(_('無法新增至購物車'));
        }

        return $item;
    }

    public static function getInstance()
    {
        static $instance;
        if ($instance) {
            return $instance;
        }

        return $instance = new static();
    }

}
