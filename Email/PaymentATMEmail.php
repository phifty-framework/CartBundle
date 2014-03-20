<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class PaymentATMEmail extends MemberOrderEmail
{
    public $templateHandle = 'payment_atm_confirming';

    public function title() {
        return _('已收到您的 ATM 匯款資料');
    }
}





