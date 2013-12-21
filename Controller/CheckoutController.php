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
        $order = new Order;
        $order->load([
            'id' => $oId,
            'token' => $token,
        ]);
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
        return $this->render("checkout_payment.html");
    }
}
