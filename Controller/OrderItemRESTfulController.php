<?php

namespace Cartbundle\Controller;

use CRUD\Controller\RESTfulResourceController;
use CartBundle\Model\OrderItem;

class OrderItemRESTfulController extends RESTfulResourceController
{
    public $recordClass = 'CartBundle\\Model\\OrderItem';

    public function loadAction($id)
    {
        $item = new OrderItem();
        $ret = $item->find($id);
        if ($ret->success) {
            $data = $item->toArray();
            $data['product'] = $item->product->toArray();
            return $this->toJson($data);
        }
        header('HTTP/1.0 404 Not Found');
        return $this->toJson(['error' => 'record not found']);
    }
}
