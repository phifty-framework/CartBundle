<?php

namespace CartBundle\Model;

use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;

class OrderItem extends \CartBundle\Model\OrderItemBase
{
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
    public function getUnitPrice()
    {
        return intval($this->product->price);
    }

    public function calculateSubtotal()
    {
        return $this->getUnitPrice() * intval($this->quantity);
    }

    public function setOrder($orderId)
    {
        $this->update(['order_id' => $orderId]);
    }

    public function beforeUpdate($args)
    {
        if (isset($args['delivery_status'])) {
            if ($this->delivery_status != $args['delivery_status']) {
                $args['delivery_status_last_updated_at'] = date('c');
            }
        }

        return $args;
    }

    public function updateDeliveryStatus($status)
    {
        $this->update(['delivery_status' => $status]);
    }

    public function setStatusPacking()
    {
        $this->setDeliveryStatus('packing');
    }

    public function setStatusUnpaid()
    {
        $this->setDeliveryStatus('unpaid');
    }

    public function setStatusShipped()
    {
        $this->setDeliveryStatus('shipped');
    }

    public function isPacking()
    {
        return $this->delivery_status == 'packing';
    }

    public function isShipped()
    {
        return $this->delivery_status == 'shipped';
    }

    public function getShippingCompany()
    {
        return $this->logistics;
    }

    public function getTrackingUrl()
    {
        if ($this->logistics_id) {
            return $this->logistics->getTrackingUrl($this->delivery_number);
        }
    }
}
