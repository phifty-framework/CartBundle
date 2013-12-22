<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;
use CartBundle\Model\Order;
use Exception;
use CartBundle\Controller\NewebPaymentController;

class OrderBaseController extends Controller
{
    /**
     * Get Current Order by 'o' and 't'
     *
     *    o=21&t=fb911675
     *
     */
    public function getCurrentOrder() {
        $oId = intval($this->request->param('o'));
        $token = $this->request->param('t');
        if ( ! $oId || ! $token ) {
            return false;
        }
        $order = new Order;
        $ret = $order->load([
            'id' => $oId,
            'token' => $token,
        ]);
        if ( ! $ret->success ||  ! $order->id ) {
            return false;
        }
        return $order;
    }

}
