<?php
namespace CartBundle\Controller\PaymentController;

use CartBundle\CartBundle;
use CartBundle\Model\Order;
use CartBundle\Model\Transaction;
use CartBundle\Controller\OrderBaseController;
use Exception;

class BasePaymentController extends OrderBaseController
{
    abstract public function getPaymentId();

    protected function getPaymentConfig($key)
    {
        $bundle = CartBundle::getInstance();
        $paymentId = $this->getPaymentId();
        return $bundle->config("Transaction.{$paymentId}.{$key}");
    }

    public function getReturnPath()
    {
        $paymentId = $this->getPaymentId();
        return "/payment/{$this->paymentId}/return";
    }


    public function getSubmitUrl()
    {
        return $this->getPaymentConfig('PaymentURL');
    }

    public function getReturnUrl()
    {
        return $this->getPaymentConfig('ReturnUrl') ?: kernel()->getBaseUrl() . $this->getReturnPath();
    }
}
