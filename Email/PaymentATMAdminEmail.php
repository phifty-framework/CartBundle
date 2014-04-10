<?php
namespace CartBundle\Email;
use EmailBundle\BaseEmail;
use MemberBundle\CurrentMember;
use UserBundle\Model\UserCollection;

class PaymentATMAdminEmail extends AdminOrderEmail
{
    public $templateHandle = 'payment_atm_admin';

    // public function from();
    // public function cc();
    // public function bcc();

    public function title() {
        return _('收到 ATM 付款通知'); 
    }
}

