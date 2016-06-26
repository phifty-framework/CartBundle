<?php

namespace CartBundle\Model;

use Exception;

class OrderItemCollection  extends \CartBundle\Model\OrderItemCollectionBase
{
    /**
     * Calculate order item level total amount (excluding coupon).
     *
     * @return int amount
     */
    public function calculateTotalAmount()
    {
        $amount = 0;
        foreach ($this as $item) {
            $amount += $item->calculateSubtotal();
        }

        return $amount;
    }

    public function calculateTotalQuantity()
    {
        return array_reduce($this->items(), function($carry, $current) {
            return $carry + intval($item->quantity);
        }, 0);
    }

    public function updateDeliveryStatus($s)
    {
        foreach ($this as $item) {
            $ret = $item->update(['delivery_status' => $s]);
            if (!$ret->success) {
                throw new Exception($ret->message);
            }
        }
    }
}
