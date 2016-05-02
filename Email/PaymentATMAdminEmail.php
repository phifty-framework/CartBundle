<?php

namespace CartBundle\Email;

class PaymentATMAdminEmail extends AdminOrderEmail
{
    public $templateHandle = 'payment_atm_admin';

    // public function from();
    // public function cc();
    // public function bcc();

    public function title()
    {
        return _('收到 ATM 付款通知');
    }
}
