<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

/**
 * Order Shipping Notification Email
 */
class OrderShippingEmail extends MemberEmail
{

    public $order;

    public function __construct($member, $order)
    {
        parent::__construct($member);
        $this->order = $this['order'] = $order;
        $lang = $this->getLang();
        $this->setTemplate( "@CartBundle/email/$lang/order_shipping.html" );
    }

    public function getTitle() {
        return _('貨品出貨通知');
    }
}





