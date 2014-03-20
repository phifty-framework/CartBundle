<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class PaymentCreditCardEmail extends MemberOrderEmail
{
    public $templateHandle = 'payment_credit_card';

    public function title() {
        return _('已收到您的信用卡款項');
    }
}





