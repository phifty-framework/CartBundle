<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;
use Exception;
use CartBundle\Controller\PaymentController\NewebPaymentController;
use CartBundle\Controller\PaymentController\ATMPaymentController;
use CartBundle\Controller\PaymentController\PODPaymentController;

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

        $controllers = [
            'atm' => new ATMPaymentController,
            'pod' => new PODPaymentController,
        ];
        if ( $cashFlow == "neweb" ) {
            $controllers['cc'] = new NewebPaymentController;
        } else {
            throw new Exception('cashflow backend is not defined.');
        }

        $paymentType = $this->request->param('payment_type');
        if ( isset($controllers[$paymentType]) ) {
            return $controllers[$paymentType]->indexAction();
        } else {
            throw new Exception('unsupported payment.');
        }
    }
}
