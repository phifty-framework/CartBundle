<?php

namespace CartBundle\Action;

use ActionKit\RecordAction\UpdateRecordAction;
use ProductBundle\Model\ProductType;
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

        $type = new ProductType;
        $type->find(intval($this->arg('product_type')));
        if (!$type->id) {
            return $this->error(_('無此產品類型'));
        }

        $quantity = intval($this->arg('quantity'));
        if ($quantity <= 0) {
            return $this->error(_('數量不可為零'));
        }
        /*
        $ret = parent::run();
        if ($ret) {
        }
        */
        $cart = Cart::getInstance();

        if ($cart->updateItem($item, $type, $quantity)) {
            $summary = $cart->getSummary();
            $summary['amount'] = $item->calculateSubtotal();
            return $this->success(_('成功更新'), $summary);
        }
        return $this->error(_('無此權限'));
    }
}
