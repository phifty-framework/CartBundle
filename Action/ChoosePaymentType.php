<?php

namespace CartBundle\Action;

use ActionKit\Action;
use CartBundle\Model\Order;

class ChoosePaymentType extends Action
{
    public function schema()
    {
        $this->param('o')->required()->label('訂單編號');
        $this->param('t')->required()->label('Security Token');
        $this->param('payment_type')->required();
    }

    public function run()
    {
        $order = new Order();
        $order->load(['id' => $this->arg('o'), 'token' => $this->arg('t')]);
        if (!$order->id) {
            return $this->error(_('錯誤'));
        }
        $ret = $order->update(['payment_type' => $this->arg('payment_type')]);
        if (!$ret->success) {
            return $this->error($ret->message);
        }

        return $this->success('設定成功');
    }
}
