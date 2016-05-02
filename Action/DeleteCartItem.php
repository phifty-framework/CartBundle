<?php

namespace CartBundle\Action;

use ActionKit\RecordAction\DeleteRecordAction;

class DeleteCartItem extends DeleteRecordAction
{
    public $recordClass = 'CartBundle\\Model\\OrderItem';

    public function run()
    {
        $orderItem = $this->getRecord();
        if ($orderItem->order_id) {
            return $this->error('Items added to an order should not be updated.');
        }

        return parent::run();
    }
}
