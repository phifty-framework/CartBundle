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
        $this->route('/payment/neweb', 'NewebPaymentController:neweb');
        $this->route('/payment/neweb/response', 'NewebPaymentController:response');
        $this->route('/payment/neweb/return', 'NewebPaymentController:return');
    }

}
