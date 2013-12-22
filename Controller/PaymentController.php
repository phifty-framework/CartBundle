<?php
namespace CartBundle\Controller;
use Phifty\Controller;

class PaymentController extends Controller
{
    public function newebAction() {
        $bundle = kernel()->bundle('CartBundle');
        $config = $bundle->config('Neweb');

        $orderId = $this->request->param('order_id');
        $orderPrefix = date('Ymd') . $orderId;

        // XXX: orderId
        return $this->render("checkout_payment_credit_card.html", [
            'config' => $config,
            'isMobile' => $this->isMobile(),
            'english' => kernel()->locale->current() != 'zh_TW',
            'orderPrefix' => $orderPrefix,
        ]);
    }

    public function newebReturnAction() {

    }

    public function newebResponseAction() {

    }
}

