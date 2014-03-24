<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use UserBundle\Email\AdminEmail;

/**
 * Order Shipping Notification Email
 *
 *     $email = new MemberOrderEmail($member, $order)
 *     $email->send();
 */
class AdminOrderPaymentEmail extends AdminEmail
{
    public $order;
    public $member;
    public $txn;

    public function __construct($member, $order, $txn)
    {
        $this->member = $this['member'] = $member;
        $this->order = $this['order'] = $order;
        $this->txn = $this['txn'] = $txn;
        parent::__construct();
    }
}





