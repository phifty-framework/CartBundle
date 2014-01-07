<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\UpdateRecordAction;
use CartBundle\Model\Order;
use CartBundle\Model\OrderCollection;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;

class ReturnOrderItem extends Action
{
    public function schema() {
        $this->param('id');
        $this->param('o');
        $this->param('t');
    }

    public function run() {
        $order = new Order([ 'id' => $this->arg('o'), 'token' => $this->arg('t') ]);
        $orderItem = new OrderItem([ 'id' => $this->arg('id') ]);

        if ( ! $order->id  || ! $orderItem->id || $orderItem->order_id != $order->id ) {
            return $this->error('錯誤的資料');
        }

        if ( $orderItem->shipping_status == "returning" || $orderItem->shipping_status == "returned" ) {
            return $this->error('此商品已申請退貨或已經退貨');
        }
        $orderItem->update([ 'shipping_status' => 'returning' ]);
        return $this->success('已經申請退貨，請等候通知。');
    }
}
