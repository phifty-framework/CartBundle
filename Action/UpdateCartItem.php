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
            return $this->success( _('成功更新'), array( 
                'amount'            => $item->calculateAmount(),
                'shipping_cost'     => $cart->calculateShippingCost(),
                'total_amount'      => $cart->calculateTotalAmount(),
                'discounted_amount' => $cart->calculateDiscountedTotalAmount(),
            ));
        } else {
            return $this->error( _('無此權限') );
        }
    }
}
