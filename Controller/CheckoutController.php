<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;

class CheckoutController extends Controller
{
    public function reviewAction() {
        // o=21&t=fb911675
        $oId = intval($this->request->param('o'));
        $token = $this->request->param('t');

        if ( ! $oId || ! $token ) {
            // XXX: show correct erro message
            return $this->redirect('/');
        }


        $order = new Order;
        $ret = $order->load([
            'id' => $oId,
            'token' => $token,
        ]);
        if ( ! $ret->success ||  ! $order->id ) {
            // XXX: show correct erro message
            return $this->redirect('/');
        }
        return $this->render("checkout_review.html", [
            'order' => $order,
        ]);
    }

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

    public function paymentAction() {
        $paymentType = $this->request->param('payment_type');
        return $this->render("checkout_payment.html", [
            'paymentType' => $paymentType,
        ]);
    }
}
