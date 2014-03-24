<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use UserBundle\Email\AdminEmail;

/**
 * Order Shipping Notification Email
 *
 *     $email = new AdminOrderPaymentEmail($member, $order, $txn)
 *     $email->send();
 */
class AdminOrderPaymentEmail extends AdminEmail
{
    public $order;
    public $member;
    public $txn;

    public $templateHandle = 'payment_admin';

    public function __construct($member, $order, $txn)
    {
        $this->member = $this['member'] = $member;
        $this->order = $this['order'] = $order;
        $this->txn = $this['txn'] = $txn;
        parent::__construct();
    }
}





