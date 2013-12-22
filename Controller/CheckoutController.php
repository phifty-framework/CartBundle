<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;
use Exception;
use CartBundle\Controller\NewebPaymentController;

class CheckoutController extends OrderBaseController
{

    public function reviewAction() {
        $order = $this->getCurrentOrder();
        if( false === $order ) {
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


    /**
     * Payment page dispatcher
     */
    public function paymentAction() {
        $bundle = kernel()->bundle('CartBundle');
        $cashFlow = $bundle->config('CashFlow');

        $paymentType = $this->request->param('payment_type');
        if ( $paymentType == "cc" ) {
            if ( $cashFlow == "neweb" ) {
                $paymentController = new NewebPaymentController;
                return $paymentController->indexAction();
            } else {
                throw new Exception('cashflow backend is not defined.');
            }
        }
        return $this->render("checkout_payment.html", [
            'paymentType' => $paymentType,
        ]);
    }

}
