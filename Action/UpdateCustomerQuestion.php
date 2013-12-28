<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\UpdateRecordAction;
use CartBundle\Model\CustomerQuestion;

class UpdateCustomerQuestion extends UpdateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\CustomerQuestion';

    public function run() {
        $cUser = kernel()->currentUser;
        if ( ! $cUser->isLogged() || ! $cUser->hasRole('admin') ) {
            return $this->error('無此權限');
        }
        return parent::run();
    }

    public function successMessage($ret) {
        return _('成功更新');
    }
}
