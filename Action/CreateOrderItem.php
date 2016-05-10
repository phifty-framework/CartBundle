<?php

namespace Cartbundle\Action;

use ActionKit\RecordAction\CreateRecordAction;
use MemberBundle\CurrentMember;

class CreateOrderItem extends CreateRecordAction
{
    public $recordClass = 'CartBundle\Model\OrderItem';

    public function run()
    {
        $currentMember = new CurrentMember();
        if ($currentMember->hasLoggedIn()) {
            $this->setArg('member_id', $currentMember->id);
        }
        $ret = parent::run();
        $orderItem = $this->getRecord();
        $data = $orderItem->toArray();
        $data['product'] = $orderItem->product->toArray();
        return $ret ? $this->success('OrderItem created', $data) : $ret;
    }
}
