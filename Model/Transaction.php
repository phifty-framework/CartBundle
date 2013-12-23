<?php
namespace CartBundle\Model;

class Transaction  extends \CartBundle\Model\TransactionBase {
    
    public function afterCreate($args) {
        // force update the payment type of the order
        if ( $this->result ) {
            $order = $this->order;
            $order->payment_type = $this->type;
            $order->setPaidAmount(intval($this->amount), $this->type == 'atm' ? 'confirming' : 'paid' );
            $order->save();
        } else {
            $this->order->update([ 'payment_status' => 'paid_error' ]);
        }
    }

}
