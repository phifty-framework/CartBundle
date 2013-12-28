<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\DeleteRecordAction;

class DeleteOrder extends DeleteRecordAction
{
    public $recordClass = 'CartBundle\\Model\\Order';

    public function run() {
        $cUser = kernel()->currentUser;

        if ( ! $cUser->isLogged() ) {
            return $this->error( _('登入時間過長，請重新登入系統。') );
        }
        if ( ! $cUser->hasRole('admin') ) {
            return $this->error( _('您的權限不足，請詢問管理員') );
        }
        return parent::run();
    }

    public function successMessage($ret) {
        return _('已成功刪除訂單');
    }
}
