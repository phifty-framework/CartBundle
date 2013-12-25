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
            return $this->error( _('權限不足') );
        }
        return parent::runValidate();
    }

    public function run() {
        foreach( $this->order_items as $item ) {
            $item->delete();
        }
        foreach( $this->transactions as $txn ) {
            $txn->delete();
        }
        return parent::run();
    }
}
