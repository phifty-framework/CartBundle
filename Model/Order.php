<?php
namespace CartBundle\Model;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Order;
use CartBundle\Model\OrderCollection;
use DateTime;

class Order  extends \CartBundle\Model\OrderBase {



    /**
     * Create Order SN from Date, transaction_times and Order id
     *
     *   {year}{month}{day}{transaction_times}{ order count by day }
     */
    const SN_FORMAT  = '%8s%02d%05d';


    /**
     * @param DateTime $date 
     * @param int $txnTimes 
     * @param int $serialNum
     */
    public function generateSN($date, $txnTimes = 1, $serialNum = null) {
        if ( is_string($date) ) {
            $date = new DateTime($date);
        }
        if ( ! $serialNum ) {
            $serialNum = OrderCollection::getCountByDay($date);
        }
        return sprintf(self::SN_FORMAT, $date->format('Ymd'), $txnTimes, $serialNum);
    }

    public function regenerateSN() 
    {
        // parse sn from the current sn
        if ( false !== sscanf($this->sn, self::SN_FORMAT, $date, $t , $serialNum) ) {
            $date = DateTime::createFromFormat('Ymd', $date);
            $this->sn = $this->generateSN($date, ++$this->transaction_times, $serialNum);
        } else {
            throw new Exception('SN generation failed.');
        }
        return $this->save();
    }

    public function afterCreate($args) 
    {
        // generate order sn with format '201309310001'
        $this->lockWrite();
        $this->update([ 'sn' => $this->generateSN($this->created_on, $this->transaction_times) ]);
        $this->unlock();
    }

    public function beforeDelete($args) {
        if ( $orderItems = $this->order_items ) {
            foreach( $this->order_items as $item ) {
                $item->delete();
            }
        }
        if ( $txns = $this->transactions ) {
            foreach( $this->transactions as $txn ) {
                $txn->delete();
            }
        }
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
            // paid status
            $this->payment_status = $status;
            $this->order_items->updateShippingStatus('processing');
        } else {
            // incomplete payment, need to confirm.
            $this->payment_status = 'paid_incomplete';
            $this->order_items->updateShippingStatus('confirming');
        }
    }

    public function getOrderViewUrl() {
        return kernel()->getHostBaseUrl() . '/order/view?' . http_build_query([ 
            'o' => $this->id,
            't' => $this->token,
        ]);
    }

}
