<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\CreateRecordAction;
use CartBundle\Model\CustomerQuestion;
use CartBundle\Email\AdminCustomerQuestionEmail;

class SubmitCustomerQuestion extends CreateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\CustomerQuestion';

    public function run() {
        $ret = parent::run();
        if ( $ret ) {
            $question = $this->getRecord();
            $email = new AdminCustomerQuestionEmail($question);
            $email->send();
        }
        return $ret;
    }

    public function successMessage($ret) {
        return _('成功送出');
    }
}
