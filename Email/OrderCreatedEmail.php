<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class OrderCreatedEmail extends MemberEmail
{
    public $order;

    public $templateHandle = 'order_created';

    public function __construct($member, $order)
    {
        parent::__construct($member);
        $this->order = $this['order'] = $order;
    }

    public function title() {
        return _('訂單建立成功');
    }
}





