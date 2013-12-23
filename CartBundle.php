<?php
namespace CartBundle;
use Phifty\Bundle;

class CartBundle extends Bundle
{
    public function assets() { return array(); }

    public function defaultConfig() { return array(); }

    public function init() 
    {
        $this->route('/=/cart/items', 'CartController:items');
        $this->route('/=/cart/calculate', 'CartController:calculate');
        $this->route('/=/cart/apply_coupon', 'CartController:applyCoupon');


        $this->route('/cart', 'CartController:index');
        $this->route('/checkout/confirm', 'CheckoutController:confirm');
        $this->route('/checkout/order', 'CheckoutController:order');

        $this->route('/order/view', 'OrderController:view');
        $this->route('/order/payment', 'OrderController:payment');


        /** routes for payment. */
        $this->route('/payment/neweb'          ,'NewebPaymentController:index');
        $this->route('/payment/neweb/response' ,'NewebPaymentController:response');
        $this->route('/payment/neweb/return'   ,'NewebPaymentController:return');

        $this->route('/payment/pod'          ,'PODPaymentController:index');
        $this->route('/payment/pod/response' ,'PODPaymentController:response');

        $this->route('/payment/atm'          ,'ATMPaymentController:index');
        $this->route('/payment/atm/response' ,'ATMPaymentController:response');

        $this->expandRoute( '/bs/order',          'OrderCRUDHandler');

        $bundle = $this;
        kernel()->event->register( 'adminui.init_menu' , function($menu) use ($bundle) {
            $menu->createCrudMenuItem( 'order', _('訂單管理') );
        });
    }

}
