<?php
namespace CartBundle\Controller\PaymentController;

use CartBundle\Model\Order;

interface ThirdPartyPaymentController
{
    /**
     * submit url for the form
     */
    public function getSubmitUrl();

    /**
     * @return array form fields in array
     */
    public function buildFormFields(Order $order, array $override = array());

    /**
     * Response action, implements the verification
     */
    public function responseAction();
}



