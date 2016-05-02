<?php

namespace CartBundle\Model;

use Exception;

class Transaction  extends \CartBundle\Model\TransactionBase
{
    public function afterCreate($args)
    {
        // force update the payment type of the order
        if ($this->result) {
            $order = $this->order;
            if ($this->type) {
                $order->payment_type = $this->type;
            }
            if ($this->amount) {
                $order->setPaidAmount(intval($this->amount), $this->type == 'atm' ? 'confirming' : 'paid');
            }
            $ret = $order->save();
            if (!$ret->success) {
                throw new Exception($ret->message);
            }
        } else {
            $this->order->update(['payment_status' => 'paid_error']);
        }
    }
}
