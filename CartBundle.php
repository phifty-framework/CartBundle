<?php

namespace CartBundle;

use Phifty\Bundle;
use CartBundle\Controller\OrderItemRESTfulController;

class CartBundle extends Bundle
{
    public function assets()
    {
        return array('cart');
    }

    public function defaultConfig()
    {
        return array(
            'RequireUTCNameAndAddress' => 'always',
            'UseProductTypeQuantity' => false,
            'ShipmentTracking' => false,
            'NoShippingFeeCondition' => array('AboveAmount' => 1500),
            'CashFlow' => 'neweb',
            'ChooseDeliveryType' => true,
            'DefaultRoutes' => false,
            'Transaction' => array(
                'Neweb' => array(
                    'MerchantNumber' => 759973,
                    'RCode' => 'abcd1234',
                    'Code' => 'abcd1234',
                    'ApproveFlag' => 1,
                    'DepositFlag' => 1,
                    'PaymentURL' => 'https://testmaple2.neweb.com.tw/NewebmPP/cdcard.jsp',
                    'OrderURL' => 'http://ibiyaya.dev/payment/neweb/response',
                    'ReturnURL' => 'http://ibiyaya.dev/payment/neweb/return',
                    'AdminURL' => 'https://testmaple2.neweb.com.tw/NewebPayment2/login.jsp',
                ),
            ),
        );
    }

    public function init()
    {
        // $this->route('/=/cart/items', 'CartController:items');
        // $this->route('/=/cart/calculate', 'CartController:calculate');
        // $this->route('/=/cart/apply_coupon', 'CartController:applyCoupon');
        if ($this->config('DefaultRoutes')) {
            $this->route('/cart', 'CartController:index');
            $this->route('/checkout/confirm', 'CheckoutController:confirm');
            $this->route('/checkout/order', 'CheckoutController:order');

            $this->route('/order/view', 'OrderController:view');
            $this->route('/order/payment', 'OrderController:payment');
            $this->route('/order/print', 'OrderController:packingList');

            $this->route('/order_item/return', 'OrderController:returnOrderItem');

        }

        /* routes for payment. */
        $this->route('/payment/neweb', 'PaymentController\\NewebPaymentController:index');
        $this->route('/payment/neweb/response', 'PaymentController\\NewebPaymentController:response');
        $this->route('/payment/neweb/return', 'PaymentController\\NewebPaymentController:return');

        $this->route('/payment/esunacq', 'PaymentController\\EsunACQPaymentController:index');
        $this->route('/payment/esunacq/return', 'PaymentController\\EsunACQPaymentController:return');

        $this->route('/payment/pod', 'PaymentController\\PODPaymentController:index');
        $this->route('/payment/pod/response', 'PaymentController\\PODPaymentController:response');

        $this->route('/payment/atm', 'PaymentController\\ATMPaymentController:index');
        $this->route('/payment/atm/response', 'PaymentController\\ATMPaymentController:response');



        // Add OrderItem actions
        $this->addRecordAction('OrderItem');

        // URL: http://ichimove.dev/=/order_item/199
        $this->kernel->restful->addResource('order_item', new OrderItemRESTfulController());

        /*
        $this->mount( '/bs/order',          'OrderCRUDHandler');
        $this->mount( '/bs/returning_order_item',  'ReturningOrderItemCRUDHandler');
        $this->mount( '/bs/customer_question', 'CustomerQuestionCRUDHandler');
        $bundle = $this;
        kernel()->event->register( 'adminui.init_menu' , function($menu) use ($bundle) {
            $folder = $menu->createMenuFolder( '電子商務' );
            $folder->createCrudMenuItem( 'order', _('訂單管理') );
            $folder->createCrudMenuItem( 'returning_order_item', _('申請退貨項目') );
            $folder->createCrudMenuItem( 'customer_question', _('客服問答管理') );

            if ( kernel()->bundle('CartBundle') ) {
                $folder->createCrudMenuItem( 'coupon', _('折價券管理') );
            }
            if ( kernel()->bundle('ShippingBundle') ) {
                $folder->createCrudMenuItem( 'shipping_company', _('物流公司管理') );
            }
        });
        */
    }
}
