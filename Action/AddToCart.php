<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\Cart;
use Exception;

class AddToCart extends Action
{
    public function schema() {
        $this->param('product_id')
            ->required()
            ->label( _('產品') );

        $this->param('quantity')
            ->required()
            ->label( _('數量') );

        $this->param('product_type')
            ->required()
            ->label( _('類型') );
    }

    public function run() {
        $cart = Cart::getInstance();
        try {
            if ( $cart->addItem( $this->arg('product_id'), $this->arg('product_type'), $this->arg('quantity') ) ) {
                return $this->success(_('成功新增至購物車'), array( 
                    'total_quantity' => $cart->calculateTotalQuantity(),
                ));
            }
        } catch ( Exception $e ) {
            return $this->error( $e->getMessage() );
        }
        return $this->error(_('不明的錯誤'));
    }
}
