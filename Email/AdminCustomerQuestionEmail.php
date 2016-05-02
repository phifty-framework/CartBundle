<?php

namespace CartBundle\Email;

use UserBundle\Email\AdminEmail;

class AdminCustomerQuestionEmail extends AdminEmail
{
    public $format = 'text/html';

    public $templateHandle = 'admin_customer_question';

    public $question;

    public function __construct($question)
    {
        parent::__construct();
        $this->question = $this['question'] = $question;
    }

    // public function from();
    // public function cc();
    // public function bcc();
}
