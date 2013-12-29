<?php
namespace CartBundle;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
use CartBundle\Model\Order;
use Exception;
use ArrayIterator;
use CartBundle\Exception\CartException;

/**
 * OrderItem object related basic tasks.
 **/
class CartBase
{
    public $storage;

    public $quantityInvalidItems = array();

    /**
     * Get item id list from storage and rebless them into objects.
     *
     * @return OrderItemCollection
     */
    public function getOrderItems() {
        $items = $this->storage->get();
        if ( count($items) ) {
            $collection = new OrderItemCollection;
            foreach( $items as $id ) {
                $item = new OrderItem(intval($id));
                if ( $item->id ) {
                    $collection->add($item);
                }
            }
            return $collection;
        }
        return array();
    }

    public function validateItemQuantity($item) {
        $t = $item->type;
        if ( !$t || !$t->id ) {
            return false;
        }
        if ( $item->quantity > $t->quantity ) {
            return false;
        }
        return true;
    }

    public function validateItem($item) {
        if (!$item->id) {
            return false;
        }
        if ( $item->order_id ) {
            return false;
        }
        $p = $item->product;
        if ( !$p || !$p->id ) {
            return false;
        }
        $t = $item->type;
        if ( !$t || !$t->id ) {
            return false;
        }
        return true;
    }


    public function isInvalidItem($item) {
        return in_array($item->id, $this->quantityInvalidItems);
    }


    public function purgeQuantityInvalidItems() {
        // using session as our storage
        $items = $this->storage->get();
        $this->quantityInvalidItems = array();
        if ( count($items) ) {
            $newItems = array();
            foreach( $items as $id ) {
                $item = new OrderItem( intval($id) );
                if ( $this->validateItem($item) && $this->validateItemQuantity($item)  ) {
                    $newItems[] = intval($id);
                }
            }
            $this->storage->set($newItems);
        }
    }

    public function validateItems() {
        // using session as our storage
        $items = $this->storage->get();
        $this->quantityInvalidItems = array();
        if ( count($items) ) {
            $newItems = array();
            foreach( $items as $id ) {
                $item = new OrderItem( intval($id) );
                if ( $this->validateItem($item) ) {
                    $newItems[] = intval($id);

                    if ( ! $this->validateItemQuantity($item) ) {
                        $this->quantityInvalidItems[] = intval($id);
                    }
                }
            }
            $this->storage->set($newItems);
        }
    }

    /**
     * Update product type or quantity of an order item
     *
     * @param integer $itemId
     * @param integer $typeId
     * @param interger $quantity
     * @return true
     */
    public function updateOrderItem($itemId, $typeId, $quantity) {
        $item = new OrderItem( intval($itemId) );
        if ( ! $item->id ) {
            throw new CartException( _('無此項目') );
        }
        if ( $item->order_id ) {
            throw new CartException( _('不可更新已經下訂之訂單項目') );
        }

        $args = array();

        if ( $typeId ) {
            $type = new ProductType(intval($typeId));
            if ( ! $type->id ) {
                throw new CartException( _('無此產品類型') );
            }
            $args['type_id'] = $type->id;
        }
        if ( $quantity ) {
            $args['quantity'] = $quantity;
        }

        if ( empty($args) ) {
            return $item;
        }

        $ret = $item->update($args);
        if ( ! $ret->success ) {
            throw new CartException(_('無法新增至購物車'));
        }
        return $item;
    }

    public function deleteOrderItem($id) {
        $item = new OrderItem(intval($id));
        // does not belongs to an order
        if ( $item->order_id ) {
            return false;
        }
        $ret = $item->delete();
        return $ret->success;
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



    static public function getInstance() {
        static $instance;
        if ( $instance ) {
            return $instance;
        }
        return $instance = new static;
    }


}



