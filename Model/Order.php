<?php
namespace CartBundle\Model;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Order;

class Order  extends \CartBundle\Model\OrderBase {

    public function afterCreate($args) 
    {
        // generate order sn with format '201309310001'
        $this->update([ 'sn' => $this->createSN() ]);

    }

    /**
     * Create Order SN from Date, transaction_times and Order id
     *
     * TODO: get serial number group by day
     */
    public function createSN() {
        if ( $this->id ) {
            return sprintf('%s%02s%08s', $this->created_on->format('Ymd'), $this->transaction_times + 1, $this->id);
        }
        return sprintf('%s%02s%08s', date('Ymd'), $this->transaction_times + 1, $this->id);
    }

    public function regenerateSN() 
    {
        $args = [
            'transaction_times' => ++$this->transaction_times,
            'sn' => $this->createSN(),
        ];
        return $this->update($args);
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
