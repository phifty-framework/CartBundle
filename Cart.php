<?php
namespace CartBundle;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Order;
use CartBundle\Exception\CartException;
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
    }

    public function removeItem($id) {
        $this->deleteOrderItem($id);
        $this->storage->remove($id);
    }

    public function addItem( $productId , $typeId, $quantity = 1) {
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
                    'quantity' => $quantity,
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

    public function calculateTotalAmount() {
        $collection = $this->getOrderItems();
        return $collection->calculateTotalAmount();
    }

    public function calculateDiscountedTotalAmount() {
        $totalAmount = $this->calculateTotalAmount();
        // get coupon discount ...
        $couponDiscount = 0;
        return $totalAmount - $couponDiscount;
    }

    public function hasCoupon() {
        return false;
    }

}




