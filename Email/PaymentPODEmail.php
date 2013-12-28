<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class PaymentPODEmail extends MemberOrderEmail
{
    public function __construct($member, $order)
    {
        parent::__construct($member, $order);
        $lang = $this->getLang();
        $this->setTemplate( "@CartBundle/email/$lang/payment_pod.html" );
    }

    public function getTitle() {
        return _('感謝您的購買！我們會儘快安排出貨');
    }
}






