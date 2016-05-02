<?php
namespace Cartbundle\Controller;
use Phifty\Controller;
use CRUD\Controller\RESTfulResourceController;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;

class OrderItemRESTfulController extends RESTfulResourceController
{
    public $recordClass = 'CartBundle\\Model\\OrderItem';

    public function loadAction($id)
    {
        $item = new OrderItem();
        $ret = $item->find($id);
        if ($ret->success) {
            return $this->toJson($item->toArray());
        }
        header('HTTP/1.0 404 Not Found');
        return $this->toJson([ 'error' => 'record not found' ]);
    }
}
