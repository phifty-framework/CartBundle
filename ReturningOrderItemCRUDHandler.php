<?php

namespace CartBundle;

use AdminUI\CRUDHandler;

class ReturningOrderItemCRUDHandler extends CRUDHandler
{
    /* CRUD Attributes */
    public $modelClass = 'CartBundle\Model\OrderItem';
    public $crudId = 'returning_order_item';
    public $templateId = 'order_item';

    public $listColumns = array('order_id', 'product', 'type', 'quantity', 'shipping_company', 'shipping_status', 'returning_reason');
    // public $filterColumns = array();
    // public $quicksearchFields = array('name');

    public $canCreate = false;
    public $canUpdate = true;
    public $canDelete = true;

    public $canBulkEdit = true;
    public $canBulkDelete = true;
    public $canBulkCopy = false;
    public $canEditInNewWindow = false;

    // public $templatePage = '@CRUD/page.html';
    // public $actionViewClass = 'AdminUI\\Action\\View\\StackView';
    // public $pageLimit = 15;
    // public $defaultOrder = array('id', 'DESC');

    public function getListTitle()
    {
        return '退貨項目';
    }

    public function getCollection()
    {
        $collection = parent::getCollection();
        $collection->where(array(
            'shipping_status' => 'returning',
        ));

        return $collection;
    }
}
