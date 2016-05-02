<?php

namespace CartBundle\Action;

use ActionKit\RecordAction\UpdateRecordAction;
use CartBundle\Cart;

/**
 * Update order item in Cart.
 */
class UpdateCartItem extends UpdateRecordAction
{
    public $recordClass = 'CartBundle\Model\OrderItem';

    public function run()
    {
        $orderItem = $this->getRecord();
        if ($orderItem->order_id) {
            return $this->error('Items added to an order should not be updated.');
        }
        /*
        $ret = parent::run();
        if ($ret) {
        }
        */
        $cart = Cart::getInstance();
        if ($item = $cart->updateOrderItem($this->arg('id'), $this->arg('product_type') , $this->arg('quantity') )) {
            $summary = $cart->getSummary();
            $summary['amount'] = $item->calculateAmount();
            return $this->success(_('成功更新'), $summary);
        }
        return $this->error(_('無此權限'));
    }
}
