<?php

namespace CartBundle\Email;

class OrderItemShippedEmail extends MemberOrderEmail
{
    public $templateHandle = 'order_item_shipped';

    public function __construct($member, $order, $items)
    {
        parent::__construct($member, $order);
        $this['order_items'] = $items;
    }

    public function title()
    {
        return _('商品出貨通知');
    }
}
