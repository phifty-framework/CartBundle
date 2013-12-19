<?php
namespace CartBundle;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Order;
use Exception;

class CartException extends Exception { }

class Cart
{
    static public function getInstance() {
        static $instance;
        if ( $instance ) {
            return $instance;
        }
        return $instance = new self;
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
        if ( ! isset($_SESSION['items']) ) {
            $_SESSION['items'] = array();
        }
        $quantity = intval($quantity);



        // XXX: find the same product and type, 
        //   if it's the same, we should simply update the quantity instead of creating new items
        $item = $this->createOrderItem($product, $foundType, $quantity);
        $_SESSION['items'][] = $item->id;
        return true;
    }

    public function validateItems() {
        // using session as our storage
        if ( isset($_SESSION['items']) ) {
            $items = array();
            foreach( $_SESSION['items'] as $id ) {
                $item = new OrderItem( intval($id) );
                if ( $item->id ) {
                    $items[] = $item->id;
                }
            }
            $_SESSION['items'] = $items;
        }
    }

    public function createOrderItem($product, $type, $quantity) {
        $item = new OrderItem;
        $ret = $item->create([
            'product_id' => $product->id,
            'type_id'    => $type->id,
            'quantity'   => $quantity,
        ]);
        if ( ! $ret->success ) {
            throw new CartException(_('無法新增至購物車'));
        }
        return $item;
    }


}




