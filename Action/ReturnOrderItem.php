<?php

namespace CartBundle\Action;

use ActionKit\Action;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;

class ReturnOrderItem extends Action
{
    public function schema()
    {
        $this->param('id');
        $this->param('o');
        $this->param('t');
    }

    public function run()
    {
        $order = new Order(['id' => $this->arg('o'), 'token' => $this->arg('t')]);
        $orderItem = new OrderItem(['id' => $this->arg('id')]);

        if (!$order->id  || !$orderItem->id || $orderItem->order_id != $order->id) {
            return $this->error('錯誤的資料');
        }

        if ($orderItem->delivery_status == 'returning' || $orderItem->delivery_status == 'returned') {
            return $this->error('此商品已申請退貨或已經退貨');
        }
        $orderItem->update(['delivery_status' => 'returning']);

        return $this->success('已經申請退貨，請等候通知。');
    }
}
