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

    public function setStatusPacking() {
        $this->update([ 'shipping_status' => 'packing' ]);
    }

    public function setStatusUnpaid() {
        $this->update([ 'shipping_status' => 'unpaid' ]);
    }

    public function setStatusTransfering() {
        $this->update([ 'shipping_status' => 'transfering' ]);
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
