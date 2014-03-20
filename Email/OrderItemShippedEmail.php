<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class OrderItemShippedEmail extends MemberOrderEmail
{
    public $templateHandle = 'order_item_shipped';

    public function __construct($member, $order, $items)
    {
        parent::__construct($member, $order);
        $this['order_items'] = $items;
    }

    public function getTitle() {
        return _('商品出貨通知');
    }
}





