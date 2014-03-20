<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class OrderCreatedEmail extends MemberOrderEmail
{
    public $templateHandle = 'order_created';

    public function title() {
        return _('訂單建立成功');
    }
}





