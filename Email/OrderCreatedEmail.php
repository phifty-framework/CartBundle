<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class OrderCreatedEmail extends MemberEmail
{

    public $order;

    public function __construct($member, $order)
    {
        parent::__construct($member);
        $this->order = $this['order'] = $order;
        $lang = $this->getLang();
        $this->setTemplate( "@CartBundle/email/$lang/order_created.html" );
    }

    public function getTitle() {
        return _('訂單建立成功');
    }
}





