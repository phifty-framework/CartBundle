<?php

namespace CartBundle\Email;

class OrderCreatedEmail extends MemberOrderEmail
{
    public $templateHandle = 'order_created';

    public function title()
    {
        return _('訂單建立成功');
    }
}
