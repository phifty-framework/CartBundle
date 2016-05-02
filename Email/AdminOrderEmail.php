<?php

namespace CartBundle\Email;

use Phifty\Message\Email;
use UserBundle\Email\AdminEmail;

/**
 * Order Shipping Notification Email.
 *
 *     $email = new MemberOrderEmail($member, $order)
 *     $email->send();
 */
class AdminOrderEmail extends AdminEmail
{
    public $order;
    public $member;

    public function __construct($member, $order)
    {
        $this->member = $this['member'] = $member;
        $this->order = $this['order'] = $order;
        parent::__construct();
    }
}
