<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;
use Exception;
use CartBundle\Controller\NewebPaymentController;

class OrderController extends OrderBaseController
{

    /**
     *   /order/view?o=23&t=B237BC
     */
    public function viewAction() {
        $order = $this->getCurrentOrder();
        if( false === $order ) {
            return $this->redirect('/');
        }
        return $this->render("order_view.html", [
            'order' => $order,
        ]);
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
        return $this->render("order_payment.html", [
            'paymentType' => $paymentType,
        ]);
    }
}
