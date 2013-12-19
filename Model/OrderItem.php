<?php
namespace CartBundle\Model;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;

class OrderItem extends \CartBundle\Model\OrderItemBase {

    /*
    public function validateType() {
        $type = new ProductType;
        $ret = $type->load($this->type_id);
        if ( ! $ret->success ) {
            return false;
        }
        return true;
    }

    public function validateQuantity() {

    }
     */
    public function getUnitPrice() {
        return intval($this->product->price);
    }

    public function calculateAmount() {
        return $this->getUnitPrice() * intval($this->quantity);
    }

}
