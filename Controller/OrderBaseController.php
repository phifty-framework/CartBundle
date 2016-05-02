<?php

namespace CartBundle\Controller;

use Phifty\Controller;
use CartBundle\Model\Order;
class OrderBaseController extends Controller
{
    public $order;

    /**
     * Get Current Order by 'o' and 't'.
     *
     *    o=21&t=fb911675
     */
    public function getCurrentOrder()
    {
        if ($this->order) {
            return $this->order;
        }

        $oId = intval($this->request->param('o'));
        $token = $this->request->param('t');
        if (!$oId || !$token) {
            return false;
        }
        $order = new Order();
        $ret = $order->load([
            'id' => $oId,
            'token' => $token,
        ]);
        if (!$ret->success ||  !$order->id) {
            return false;
        }

        return $this->order = $order;
    }

    public function getBundle()
    {
        return kernel()->bundle('CartBundle');
    }
}
