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

    public function setOrder($orderId) {
        $this->update([ 'order_id' => $orderId ]);
    }


    public function beforeUpdate($args) {
        if ( isset($args['shipping_status']) ) {
            if ( $this->shipping_status != $args['shipping_status'] ) {
                $args['shipping_status_last_update'] = date('c');
            }
        }
        return $args;
    }

    public function setShippingStatus($status) 
    {
        $this->update([ 'shipping_status' => $status ]);
    }

    public function setStatusPacking() {
        $this->setShippingStatus('packing');
    }

    public function setStatusUnpaid() {
        $this->setShippingStatus('unpaid');
    }

    public function setStatusShipped() {
        $this->setShippingStatus('shipped');
    }

    public function getShippingCompany() {
        return $this->shipping_company;
    }

    public function getTrackingUrl() {
        if ( $this->shipping_id ) {
            return $this->getShippingCompany()->getTrackingUrl($this->shipping_id);
        }
    }
}
