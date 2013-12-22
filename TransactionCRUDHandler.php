<?php
namespace CartBundle;
use Phifty\Bundle;
use AdminUI\CRUDHandler;

class TransactionCRUDHandler extends CRUDHandler
{
    /* CRUD Attributes */
    public $modelClass = 'CartBundle\Model\Transaction';
    public $crudId     = 'transaction';

    // public $listColumns = array( 'id', 'thumb', 'name' , 'lang' , 'subtitle' , 'sn' );
    // public $quicksearchFields = array('name');

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

    public function init()
    {
        $this->plugin = \CartBundle\CartBundle::getInstance();
        parent::init();
    }

    public function getCollection()
    {
        return parent::getCollection();
    }
}

