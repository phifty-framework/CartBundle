<?php
namespace CartBundle\Model;

class Order  extends \CartBundle\Model\OrderBase {

    public function calculateOriginalTotalAmount() {
        $totalAmount = $this->shipping_cost;
        foreach( $this->order_items as $orderItem ) {
            $totalAmount += $orderItem->calculateAmount();
        }
        return $totalAmount;
    }

}
