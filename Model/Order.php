<?php

namespace CartBundle\Model;

use DateTime;

class Order  extends \CartBundle\Model\OrderBase
{
    /**
     * Create Order SN from Date, and Order id.
     *
     *   {year}{month}{day}{order count by day}
     *
     *   20140102 0005
     *
     *   12 charactors
     */
    const SN_FORMAT = '%8s%04d';

    public function dataLabel()
    {
        return $this->sn;
    }

    /**
     * @param DateTime $date
     * @param int      $txnTimes
     * @param int      $serialNum
     */
    public function generateSN($date,  $serialNum = null)
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        if (!$serialNum) {
            $serialNum = OrderCollection::getCountByDay($date);
        }

        return sprintf(self::SN_FORMAT, $date->format('Ymd'), $serialNum);
    }

    public function regenerateSN()
    {
        // parse sn from the current sn
        if (false !== sscanf($this->sn, self::SN_FORMAT, $date, $serialNum)) {
            $date = DateTime::createFromFormat('Ymd', $date);
            $this->sn = $this->generateSN($date, $serialNum);
        } else {
            throw new Exception('SN generation failed.');
        }

        return $this->save();
    }

    public function afterCreate($args)
    {
        // generate order sn with format '201309310001'
        // fixme: sn lock 
        // $this->lockWrite();
        $this->update(['sn' => $this->generateSN($this->created_on)]);
        // $this->unlock();
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
