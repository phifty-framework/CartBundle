<?php
namespace CartBundle\Model;

class Order  extends \CartBundle\Model\OrderBase {

    public function afterCreate($args) {
        // generate order sn with format '201309310001'
        $this->update([ 'sn' => sprintf('%s%04s',date('Ymd'), $this->id) ]);
    }

    public function calculateOriginalTotalAmount() {
        $totalAmount = $this->shipping_cost;
        foreach( $this->order_items as $orderItem ) {
            $totalAmount += $orderItem->calculateAmount();
        }
        return $totalAmount;
    }

    public function setPaidAmount($amount, $status = 'paid') {
        $this->paid_amount = $amount;
        if ( $this->paid_amount >= $this->total_amount ) {
            $this->payment_status = $status;
        } else {
            $this->payment_status = 'paid_incomplete';
        }
    }

}
