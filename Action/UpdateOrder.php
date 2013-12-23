<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\UpdateRecordAction;

class UpdateOrder extends UpdateRecordAction
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
