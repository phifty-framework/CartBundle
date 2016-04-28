<?php
namespace Cartbundle\Action;
use ActionKit\RecordAction\CreateRecordAction;

class CreateOrderItem extends CreateRecordAction
{
    public $recordClass =  'CartBundle\Model\OrderItem';

    public function run()
    {
        $ret = parent::run();
        $orderItem = $this->getRecord();
        $data = $orderItem->toArray();
        $data['product'] = $orderItem->product->toArray();
        return $ret ? $this->success('OrderItem created', $data) : $ret;
    }
}




