<?php

namespace CartBundle\Email;

class PaymentATMEmail extends MemberOrderEmail
{
    public $templateHandle = 'payment_atm_confirming';

    public function title()
    {
        return _('已收到您的 ATM 匯款資料');
    }
}
