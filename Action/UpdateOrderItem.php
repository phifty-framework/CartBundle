<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\Cart;

class UpdateOrderItem extends Action
{
    public function schema() {
        $this->param('id')
            ->required();

        $this->param('product_type');

        $this->param('quantity');
    }

    public function run() {
        $cart = Cart::getInstance();
        $item = $cart->updateOrderItem( $this->arg('id'), $this->arg('product_type') , $this->arg('quantity') );
        return $this->success( _('成功更新'), array( 
            'amount' => $item->calculateAmount(),
            'total_amount' => $cart->calculateTotalAmount(),
            'discounted_amount' => $cart->calculateDiscountedTotalAmount(),
        ));
    }
}
