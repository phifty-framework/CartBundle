<?php
namespace CartBundle\Controller\PaymentController;
use Phifty\Controller;
use CartBundle\Controller\OrderBaseController;

class ATMPaymentController extends OrderBaseController
{
    public function indexAction() {
        return $this->render("order_payment_atm.html", [ 
            'order' => $this->getCurrentOrder(),
        ]);
    }

    public function responseAction() {
        return $this->render("order_payment_atm.html");
    }
}
