<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;
use CartBundle\Model\OrderCollection;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
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
     * 貨運清單頁面
     *
     * http://ibiyaya.dev/order/packing_list?o=69&t=67eba91a
     */
    public function packingListAction() {
        $order = $this->getCurrentOrder();
        if( false === $order ) {
            return $this->redirect('/');
        }
        return $this->render("@CartBundle/order/print.html", [
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
            return $this->redirect('/');
        }
    }

    public function returnOrderItemAction() {
        $itemId = intval($this->request->param('oi'));
        $order = $this->getCurrentOrder();
        if( false === $order || ! $itemId ) {
            return $this->redirect('/');
        }

        $orderItem = new OrderItem($itemId);
        if ( ! $orderItem->id || $orderItem->order_id != $order->id ) {
            return $this->redirect('/');
        }
        return $this->render("order_item_return.html", [
            'order' => $order,
            'orderItem' => $orderItem,
        ]);
    }

}
