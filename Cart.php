<?php
namespace CartBundle;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Order;
use CartBundle\Exception\CartException;
use CouponBundle\Model\Coupon;
use Exception;
use ArrayIterator;
use IteratorAggregate;

/**
 * Contains the logics of Cart
 */
class Cart extends CartBase
{

    public function __construct() {
        // TODO: provide options to specify storage engine.
        $this->storage = new SessionCartStorage;
        $this->validateItems();
    }

    public function removeItem($id)
    {
        if ( $this->deleteOrderItem($id) ) {
            $this->storage->remove($id);
        }
    }

    public function addItem($productId, $typeId, $quantity = 1)
    {
        $product = new Product( intval($productId) );
        if ( ! $product->id ) {
            throw new CartException(_("找不到商品"));
        }

        $foundType = null;
        foreach( $product->types as $type ) {
            if ( intval($type->id) === intval($typeId) ) {
                $foundType = $type;
                break;
            }
        }
        if ( ! $foundType ) {
            throw new CartException(_("此產品無此類型"));
        }

        if ( $foundType->quantity < $quantity ) {
            // XXX: warning...
        }

        // Create the order item with session here....
        $quantity = intval($quantity);


        // find the same product and type, 
        // if it's the same, we should simply update the quantity instead of creating new items
        $foundExistingOrderItem = false;
        $items = $this->getOrderItems();
        foreach( $items as $item ) {
            if ( $item->product_id == $product->id && $item->type_id == $foundType->id ) {
                $item->update(array(
                    'quantity' => intval($item->quantity) + $quantity,
                ));
                $foundExistingOrderItem = true;
            }
        }
        if ( ! $foundExistingOrderItem ) {
            $item = $this->createOrderItem($product, $foundType, $quantity);
            $this->storage->add( $item->id );
        }
        return true;
    }

    public function calculateTotalQuantity() {
        $collection = $this->getOrderItems();
        return $collection->calculateTotalQuantity();
    }

    public function calculateTotalAmount() {
        $collection = $this->getOrderItems();
        return $collection->calculateTotalAmount();
    }

    public function calculateDiscountedTotalAmount() {
        $totalAmount = $this->calculateTotalAmount();
        if ( $coupon = $this->loadSessionCoupon() ) {
            return $coupon->calcualteDiscount($totalAmount);
        }
        return $totalAmount;
    }


    /**
     * Coupon related logics
     */
    public function applyCoupon($coupon) {
        // always validate coupon
        if ( $coupon->isValid() ) {
            $_SESSION['coupon_code'] = $coupon->coupon_code;
            return true;
        }
        return false;
    }

    public function loadSessionCoupon()
    {
        if ( isset($_SESSION['coupon_code']) ) {
            $coupon = new Coupon([ 'coupon_code' => $_SESSION['coupon_code'] ]);
            // always validate coupon
            if ( $coupon->id && $coupon->isValid() ) {
                return $coupon;
            }
            // if it's invalid coupon, just delete the sesssion
            unset($_SESSION['coupon_code']);
        }
    }

    public function cleanUp() {
        unset($_SESSION['coupon_code']);
        $this->storage->cleanUp();
    }

}




