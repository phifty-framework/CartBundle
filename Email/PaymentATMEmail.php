<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class PaymentATMEmail extends MemberOrderEmail
{
    public function __construct($member, $order)
    {
        parent::__construct($member, $order);
        $lang = $this->getLang();
        $this->setTemplate( "@CartBundle/email/$lang/payment_atm_confirming.html" );
    }

    public function getTitle() {
        return _('已收到您的 ATM 匯款資料');
    }
}





