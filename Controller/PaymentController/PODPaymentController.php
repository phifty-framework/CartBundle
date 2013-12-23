<?php
namespace CartBundle\Controller\PaymentController;
use CartBundle\Model\Order;
use CartBundle\Model\Transaction;
use CartBundle\Controller\OrderBaseController;
use Exception;

class PODPaymentController extends OrderBaseController
{
    public function indexAction() {
        return $this->render("order_payment_pod.html");
    }

    public function responseAction() {
        return $this->render("order_payment_pod.html");
    }
}
