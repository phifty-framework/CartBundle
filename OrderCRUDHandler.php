<?php
namespace CartBundle;
use Phifty\Bundle;
use AdminUI\CRUDHandler;

class OrderCRUDHandler extends CRUDHandler
{
    /* CRUD Attributes */
    public $modelClass = 'CartBundle\Model\Order';
    public $crudId     = 'order';

    public $listColumns = array(
        'id', 'sn', 'buyer_name', 'buyer_cellphone' , 
        'payment_type', 
        'payment_status',
        'coupon_code' ,
        'total_amount', 
        'discount_amount', 
        'paid_amount', 
        'created_on',
    );

    public $quicksearchFields = array('sn', 'buyer_name', 'buyer_phone', 'buyer_email','buyer_address');

    public $filterColumns = array('payment_type', 'payment_status');

    public $canCreate = true;
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


    public function getCollection()
    {
        return parent::getCollection();
    }
}

