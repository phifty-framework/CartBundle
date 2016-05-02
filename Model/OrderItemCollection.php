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
            $amount += $item->calculateAmount();
        }

        return $amount;
    }

    public function calculateTotalQuantity()
    {
        $quantity = 0;
        foreach ($this as $item) {
            $quantity += $item->quantity;
        }

        return $quantity;
    }

    public function updateShippingStatus($s)
    {
        foreach ($this as $item) {
            $ret = $item->update(['shipping_status' => $s]);
            if (!$ret->success) {
                throw new Exception($ret->message);
            }
        }
    }
}
