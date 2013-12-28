<?php
namespace CartBundle\Email;
use Phifty\Message\Email;
use MemberBundle\Email\MemberEmail;

class CustomerQuestionReplyEmail extends MemberEmail
{
    public function __construct($member, $question)
    {
        parent::__construct($member);
        $this['question'] = $question;
        $lang = $this->getLang();
        $this->setTemplate("@CartBundle/email/$lang/customer_question_reply.html");
    }

    public function getTitle() {
        return _('您所發問的問題，已經獲得我們的客服回答囉');
    }

}





