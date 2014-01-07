<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\Cart;

class RemoveCartItem extends Action
{
    public function schema() {
        $this->param('id')
            ->label( _('項目 ID') )
            ->required();
    }

    public function run() {
        $cart = Cart::getInstance();
        if ( $cart->removeItem( intval($this->arg('id')) ) ) {
            return $this->success( _('已從購物車移除'), array(
                'shipping_cost'     => $cart->calculateShippingCost(),
                'total_amount'      => $cart->calculateTotalAmount(),
                'discounted_amount' => $cart->calculateDiscountedTotalAmount(),
            ));
        }
        return $this->error( _('無法移除') );
    }
}
