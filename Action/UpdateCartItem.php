<?php

namespace CartBundle\Action;

use ActionKit\RecordAction\UpdateRecordAction;
use ProductBundle\Model\ProductType;
use CartBundle\Cart;
use CartBundle\CartBundle;

/**
 * Update order item in Cart.
 */
class UpdateCartItem extends UpdateRecordAction
{
    public $recordClass = 'CartBundle\Model\OrderItem';

    public function run()
    {
        $cart   = Cart::getInstance();
        $bundle = CartBundle::getInstance();
        $item   = $this->getRecord();
        if ($item->order_id) {
            return $this->error('Items added to an order should not be updated.');
        }

        $type = null;
        if ($bundle->config('UseProductType') && $this->arg('product_type')) {
            $type = new ProductType;
            $ret = $type->find(intval($this->arg('product_type')));
            if ($ret->error) {
                return $this->error(_('無此產品類型'));
            }
        }

        $quantity = intval($this->arg('quantity'));
        if ($quantity <= 0) {
            return $this->error(_('數量不可為零或小於零。'));
        }
        /*
        $ret = parent::run();
        if ($ret) {
        }
        */
        if ($cart->updateItem($item, $type, $quantity)) {
            $summary = $cart->getSummary();
            $summary['amount'] = $item->calculateSubtotal();
            return $this->success(_('成功更新'), $summary);
        }
        return $this->error(_('無此權限'));
    }
}
