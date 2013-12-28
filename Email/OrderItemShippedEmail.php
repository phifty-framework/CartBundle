<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class OrderItemShippedEmail extends MemberOrderEmail
{
    public function __construct($member, $order, $items)
    {
        parent::__construct($member, $order);
        $this['order_items'] = $items;
        $lang = $this->getLang();
        $this->setTemplate( "@CartBundle/email/$lang/order_item_shipped.html" );
    }

    public function getTitle() {
        return _('商品出貨通知');
    }
}





