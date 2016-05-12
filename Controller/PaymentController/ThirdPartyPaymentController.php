<?php
namespace CartBundle\Controller\PaymentController;

use CartBundle\Model\Order;

interface ThirdPartyPaymentController
{
    /**
     * submit url for the form
     *
     * @return string
     */
    public function getSubmitUrl();

    /**
     * The return path is used to mount controllers and tell the 3rd party
     * server to redirect back.
     *
     * @return string
     */
    public function getReturnPath();

    /**
     *
     * @return url for 3rd party to redirect.
     */
    public function getReturnUrl();


    /**
     * @return array form fields in array
     */
    public function buildFormFields(Order $order, array $override = array());





    /**
     * translate response form fields
     *
     * @return array
     */
    public function translateResponseFields(array $params);

    /**
     * Response action, implements the verification
     */
    public function returnAction();



}




