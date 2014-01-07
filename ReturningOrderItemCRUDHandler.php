<?php
namespace CartBundle;
use Phifty\Bundle;
use AdminUI\CRUDHandler;

class ReturningOrderItemCRUDHandler extends CRUDHandler
{
    /* CRUD Attributes */
    public $modelClass = 'CartBundle\Model\OrderItem';
    public $crudId     = 'returning_order_item';
    public $templateId = 'order_item';

    // public $listColumns = array( 'id', 'thumb', 'name' , 'lang' , 'subtitle' , 'sn' );
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


    public function getLabel() 
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

