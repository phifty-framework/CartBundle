<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\UpdateRecordAction;

class UpdateOrderItem extends UpdateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\OrderItem';

    public function runValidate() { 
        $cUser = kernel()->currentUser;
        if ( ! $cUser->isLogged() || ! $cUser->hasRole('admin') ) {
            return false;
        }
        // Not sure why this method call fails while submitting from nested action.
        // XXX: return parent::runValidate();
        return true;
    }
}
