<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Model\Order;
use Exception;

class NewebPaymentController extends Controller
{
    public function indexAction() {
        $bundle = kernel()->bundle('CartBundle');
        $config = $bundle->config;

        $orderId = intval($this->request->param('o'));
        $token   = $this->request->param('t');
        if ( ! $orderId ) {
            die('parameter error');
            return $this->redirect('/');
        }

        $order = new Order;
        $ret = $order->load([
            'id' => $orderId,
            'token' => $token,
        ]);
        if ( ! $ret->success ||  ! $order->id ) {
            // XXX: show correct erro message
            die('order not found');
            return $this->redirect('/');
        }

        if ( $order->created_on ) {
            $orderPrefix = sprintf('%s%04s', $order->created_on->format('Ymd') , $order->id );
        } else {
            $orderPrefix = sprintf('%s%04s', date('Ymd') , $order->id );
        }

        if ( ! isset($config['Transaction']['Neweb']['MerchantNumber']) ) {
            throw new Exception('Transaction.Neweb.MerchantNumber is required.');
        }
        if ( ! isset($config['Transaction']['Neweb']['Code']) ) {
            throw new Exception('Transaction.Neweb.Code is required.');
        }

        $checksum = md5( $config['Transaction']['Neweb']['MerchantNumber'] 
            . $orderPrefix
            . $config['Transaction']['Neweb']['Code']
            . $order->total_amount);

        return $this->render("checkout_payment_credit_card.html", [
            'config' => $config,
            'isMobile' => $this->isMobile() ? 1 : 0,
            'english' => kernel()->locale->current() != 'zh_TW' ? 1 : 0,
            'order' => $order,
            'orderPrefix' => $orderPrefix,
            'checksum' => $checksum,
        ]);
    }

    public function returnAction() {

    }

    public function responseAction() {

    }
}

