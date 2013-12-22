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

    public function setPaidAmount($amount) {
        $this->paid_amount = $amount;
        if ( $this->paid_amount >= $this->total_amount ) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'paid_incomplete';
        }
    }

}
