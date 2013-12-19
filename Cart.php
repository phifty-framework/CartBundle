<?php
namespace CartBundle;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Order;
use Exception;
use ArrayIterator;
use IteratorAggregate;

class CartException extends Exception { }

class Cart
{

    public $storage;

    public function __construct() {
        $this->storage = new SessionCartStorage;
    }

    static public function getInstance() {
        static $instance;
        if ( $instance ) {
            return $instance;
        }
        return $instance = new self;
    }

    public function updateOrderItem( $productId , $typeId, $quantity = 1) {
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

        // XXX: find the same product and type, 
        //   if it's the same, we should simply update the quantity instead of creating new items
        $item = $this->createOrderItem($product, $foundType, $quantity);

        $this->storage->add( $item->id );
        return true;
    }

    public function validateItems() {
        // using session as our storage
        $items = $this->storage->get();
        if ( count($items) ) {
            $newItems = array();
            foreach( $items as $id ) {
                $item = new OrderItem( intval($id) );
                if ( $item->id ) {
                    $newItems[] = $item->id;
                }
            }
            $this->storage->set($newItems);
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




