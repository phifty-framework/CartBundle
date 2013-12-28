<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\CreateRecordAction;
use CartBundle\Model\CustomerQuestion;

class SubmitCustomerQuestion extends CreateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\CustomerQuestion';

    public function run() {
        return parent::run();
    }

    public function successMessage($ret) {
        return _('成功送出');
    }
}
