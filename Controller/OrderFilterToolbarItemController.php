<?php

namespace CartBundle\Controller;

use CRUD\Controller\ToolbarItemController;

class OrderFilterToolbarItemController extends ToolbarItemController
{
    public function __construct()
    {
        $this->init();
    }

    public function getFieldName()
    {
        return '_order_item_shipping_status';
    }

    public function controlAction()
    {
        $fieldName = $this->getFieldName();
        $handler = $this->getHandler();
        $model = new \CartBundle\Model\OrderItem();
        $action = $model->asCreateAction();
        $widget = $action->getParam('shipping_status')->createWidget(null, array('allow_empty' => true));
        $widget->name = '_filter_'.$fieldName;

        return $this->render('@CRUD/filter.html', array('widget' => $widget));
    }

    public function handleCollection($collection)
    {
        $fieldName = $this->getFieldName();
        $handler = $this->getHandler();
        $value = $handler->request->param('_filter_'.$fieldName);
        if ($value !== null && $value !== '') {
            // $collection->where()->equal('order_items.shipping_status','unpaid');
            $collection->join(new \CartBundle\Model\OrderItem(), 'INNER');
            $collection->groupBy('m.id');
            $collection->where()->equal('order_items.shipping_status', $value);
        }
    }
}
