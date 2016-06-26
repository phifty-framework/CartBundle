<?php

namespace CartBundle\Model;

use ProductBundle\Model\Product;
use ProductBundle\Model\ProductType;
use RuntimeException;

class OrderItem extends \CartBundle\Model\OrderItemBase
{


    /**
     * Check if there is a product type, then we deduct the quantity from 
     * that product type.
     *
     */
    public function deductQuantity()
    {
        if ($this->type_id && $this->type->id) {
            return $this->type->deductQuantity($this->quantity);
        } else if ($this->product_id && $this->product->id) {
            return $this->product->deductQuantity($this->quantity);
        } else {
            throw new RuntimeException("Can not deduct quantity, both product and type are not found.");
        }
    }

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
                $args['delivery_status_last_updated_at'] = date('Y-m-d H:i:s');
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
