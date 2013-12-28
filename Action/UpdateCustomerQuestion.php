<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\UpdateRecordAction;
use CartBundle\Model\CustomerQuestion;
use CartBundle\Email\CustomerQuestionReplyEmail;

class UpdateCustomerQuestion extends UpdateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\CustomerQuestion';

    public function run() {
        $cUser = kernel()->currentUser;
        if ( ! $cUser->isLogged() || ! $cUser->hasRole('admin') ) {
            return $this->error('無此權限');
        }
        $ret = parent::run();
        if ( $ret ) {
            $question = $this->getRecord();
            $email = new CustomerQuestionReplyEmail($question->member, $question);
            $email->send();
        }
        return $ret;
    }

    public function successMessage($ret) {
        return _('成功更新');
    }
}
