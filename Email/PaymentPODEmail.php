<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class PaymentPODEmail extends MemberOrderEmail
{
    public $templateHandle = 'payment_pod';

    public function title() {
        return _('感謝您的購買！我們會儘快安排出貨');
    }
}






