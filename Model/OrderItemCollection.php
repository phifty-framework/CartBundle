<?php

namespace CartBundle\Model;

use Exception;
use CartBundle\Model\OrderItemCollectionBase;

class OrderItemCollection extends OrderItemCollectionBase
{
    /**
     * Calculate order item level total amount (excluding coupon).
     *
     * @return int amount
     */
    public function calculateTotalAmount()
    {
        return array_reduce($this->items(), function($carry, $current) {
            return $carry + intval($current->calculateSubtotal());
        }, 0);
    }

    public function calculateTotalQuantity()
    {
        return array_reduce($this->items(), function($carry, $current) {
            return $carry + intval($current->quantity);
        }, 0);
    }

    public function updateDeliveryStatus($s)
    {
        foreach ($this as $item) {
            $ret = $item->update(['delivery_status' => $s]);
            if ($ret->error) {
                throw new RuntimeException($ret->message);
            }
        }
    }
}
