<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\Cart;

class UpdateCartItem extends Action
{
    public function schema() {
        $this->param('id')
            ->required();

        $this->param('product_type');

        $this->param('quantity');
    }

    public function run() {
        $cart = Cart::getInstance();
        if ($item = $cart->updateOrderItem( $this->arg('id'), $this->arg('product_type') , $this->arg('quantity') ) ) {
            $summary = $cart->getSummary();
            $summary['amount'] = $item->calculateAmount();
            return $this->success( _('成功更新'), $summary);
        } else {
            return $this->error( _('無此權限') );
        }
    }
}
