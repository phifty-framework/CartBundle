<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\DeleteRecordAction;

class DeleteOrder extends DeleteRecordAction
{
    public $recordClass = 'CartBundle\\Model\\Order';

    public function runValidate() { 
        $cUser = kernel()->currentUser;
        if ( ! $cUser->isLogged() || ! $cUser->hasRole('admin') ) {
            return false;
        }
        return parent::runValidate();
    }
}
