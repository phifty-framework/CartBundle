<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class PaymentCreditCardEmail extends MemberOrderEmail
{
    public function __construct($member, $order)
    {
        parent::__construct($member, $order);
        $lang = $this->getLang();
        $this->setTemplate( "@CartBundle/email/$lang/payment_credit_card.html" );
    }

    public function getTitle() {
        return _('已收到您的信用卡款項');
    }
}





