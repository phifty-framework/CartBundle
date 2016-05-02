<?php

namespace CartBundle\Action;

use ActionKit\RecordAction\DeleteRecordAction;

class DeleteOrderItem extends DeleteRecordAction
{
    public $recordClass = 'CartBundle\\Model\\OrderItem';

    public function runValidate()
    {
        $cUser = kernel()->currentUser;
        if (!$cUser->isLogged() || !$cUser->hasRole('admin')) {
            return $this->error(_('權限不足'));
        }

        return parent::runValidate();
    }
}
