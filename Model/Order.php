<?php

namespace CartBundle\Model;

use DateTime;
use CartBundle\Model\SequenceEntitySchema;
use CartBundle\Model\SequenceEntity;

class Order  extends \CartBundle\Model\OrderBase
{
    public function dataLabel()
    {
        return $this->sn;
    }

    public static function generateSN()
    {
        $sequence = new SequenceEntity;
        $sequence->loadOrCreate([
            'handle' => 'default-order-seq',
            'prefix' => 'Y',
            'pad_length' => 12,
            'pad_char' => '0',
            'start_id' => 1,
            'last_id' => 1,
        ], 'handle');
        $sn = $sequence->getNextId();
        return $sn;
    }

    public function afterCreate($args)
    {
        // fixme: select for update...
        $this->update(['sn' => self::generateSN() ]);
    }

    public function beforeDelete($args)
    {
        if ($orderItems = $this->order_items) {
            foreach ($this->order_items as $item) {
                $item->delete();
            }
        }
        if ($txns = $this->transactions) {
            foreach ($this->transactions as $txn) {
                $txn->delete();
            }
        }
    }

    public function calculateOriginalTotalAmount()
    {
        return array_reduce($this->order_items, function($carry, $orderItem) {
            return $carry + $orderItem->calculateSubtotal();
        }, $this->shipping_fee);
    }

    public function setPaidAmount($amount, $status = 'paid')
    {
        $this->paid_amount = $amount;
        if ($this->paid_amount >= $this->total_amount) {
            // paid status
            $this->payment_status = $status;
            $this->order_items->updateDeliveryStatus('processing');
        } else {
            // incomplete payment, need to confirm.
            $this->payment_status = 'paid_incomplete';
            $this->order_items->updateDeliveryStatus('confirming');
        }
    }

    public function beforeUpdate($args = array())
    {
        if (isset($args['payment_status'])) {
            if ($args['payment_status'] != $this->payment_status) {
                $args['payment_status_last_update'] = date('c');
            }
        }

        return $args;
    }

    public function afterUpdate($args = array())
    {
    }

    public function getOrderViewUrl()
    {
        return kernel()->getBaseUrl().'/order/view?'.http_build_query([
            'o' => $this->id,
            't' => $this->token,
        ]);
    }

    public function delete()
    {
        return $this->update(array('is_deleted' => true));
    }
}
