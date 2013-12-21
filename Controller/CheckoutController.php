<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;

class CheckoutController extends Controller
{
    public function reviewAction() {
        return $this->render("checkout_review.html", [
        ]);
    }

    public function confirmAction() {
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        if ( ! $orderItems || empty($orderitems) ) {
            return $this->redirect('/cart');
        }
        return $this->render("checkout_confirm.html", [
        ]);
    }

    public function orderAction() {
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        if ( ! $orderItems || empty($orderitems) ) {
            return $this->redirect('/cart');
        }
        return $this->render("checkout_order.html");
    }

    public function paymentAction() {
        return $this->render("checkout_payment.html");
    }
}
