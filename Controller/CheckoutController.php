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

    public function orderAction() {
        return $this->render("checkout_order.html");
    }

    public function paymentAction() {
        return $this->render("checkout_payment.html");
    }
}
