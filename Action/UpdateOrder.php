<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\UpdateRecordAction;
use CartBundle\Email\OrderItemShippedEmail;

class UpdateOrder extends UpdateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\Order';


    public function run() {
        $cUser = kernel()->currentUser;
        if ( ! $cUser->isLogged() || ! $cUser->hasRole('admin') ) {
            return $this->error( _('權限不足') );
        }

        $_orderItemStatus = array();
        $order = $this->getRecord();
        foreach( $order->order_items as $item ) {
            $_orderItemStatus[ $item->id ] = $item->shipping_status;
        }
        if ($ret = parent::run()) {
            $shippedItems = array();
            $order->clearInternalCache();
            foreach( $order->order_items as $item ) {
                if ( isset($_orderItemStatus[ $item->id ]) ) {
                    if ( $_orderItemStatus[ $item->id ] != $item->shipping_status ) {
                        if ( $item->shipping_status == "shipped" ) {
                            $shippedItems[] = $item;
                        }
                    }
                }
            }
            if ( count($shippedItems) ) {
                $orderItemShippedEmail = new OrderItemShippedEmail($order->member, $order, $shippedItems);
                $orderItemShippedEmail->send();
            }
        }
        return $ret;
    }

    public function successMessage($ret) {
        return _('成功更新訂單資料。');
    }

}

