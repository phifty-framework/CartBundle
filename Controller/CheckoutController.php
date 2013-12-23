<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;
use Exception;
use CartBundle\Controller\NewebPaymentController;

class CheckoutController extends OrderBaseController
{

    public function confirmAction() {
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        if ( ! $orderItems || empty($orderItems) ) {
            return $this->redirect('/cart');
        }
        return $this->render("checkout_confirm.html", [
        ]);
    }


    public function orderAction() {
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        if ( ! $orderItems || empty($orderItems) ) {
            return $this->redirect('/cart');
        }
        return $this->render("checkout_order.html");
    }

}
