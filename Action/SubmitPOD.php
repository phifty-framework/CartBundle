<?php

namespace CartBundle\Action;

use ActionKit\Action;
use CartBundle\Model\Order;
use CartBundle\Email\PaymentPODEmail;
use CartBundle\Email\AdminOrderPaymentEmail;

class SubmitPOD extends Action
{
    public function schema()
    {
        $this->param('o')
            ->required()
            ->label(_('訂單編號'))
            ;

        $this->param('t')
            ->required()
            ->label(_('訂單安全碼'))
            ;

        /*
        $this->param('pod_time')
            ->required()
            ->label( _('貨到付款時間') )
            ;
         */
    }

    public function run()
    {
        $order = new Order();
        $order->load(['id' => $this->arg('o'), 'token' => $this->arg('t')]);
        if (!$order->id) {
            return $this->error(_('參數錯誤'));
        }
        $ret = $order->update([
            // 'pod_time' => $this->arg('pod_time'),
            'payment_status' => 'unpaid',
            'payment_type' => 'pod',
        ]);
        if ($ret->success) {
            $email = new PaymentPODEmail($order->member, $order);
            $email->send();

            $adminEmail = new AdminOrderPaymentEmail($order->member, $order);
            $adminEmail->send();

            return $this->success(_('感謝您的訂購，我們會盡快出貨'));
        }

        return $this->error(__('錯誤 %1'), $ret->message);
    }
}
