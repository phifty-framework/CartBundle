<?php

namespace CartBundle\Controller\PaymentController;

use CartBundle\Model\Order;
use CartBundle\Controller\OrderBaseController;

class PODPaymentController extends OrderBaseController
{
    public function indexAction()
    {
        return $this->render('order_payment_pod.html', [
            'order' => $this->getCurrentOrder(),
        ]);
    }

    public function responseAction()
    {
        return $this->render('order_payment_pod.html');
    }
}
