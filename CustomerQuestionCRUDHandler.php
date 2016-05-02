<?php

namespace CartBundle;

use AdminUI\CRUDHandler;

class CustomerQuestionCRUDHandler extends CRUDHandler
{
    /* CRUD Attributes */
    public $modelClass = 'CartBundle\\Model\\CustomerQuestion';
    public $crudId = 'customer_question';

    public $listColumns = array('id', 'question_title', 'question', 'answer', 'order',  'member', 'question_time', 'answer_time');

    // public $filterColumns = array();
    // public $quicksearchFields = array('name');

    public $canCreate = false;
    public $canUpdate = true;
    public $canDelete = false;

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
        parent::init();
        $this->setFormatter('question', function ($record) {
            if ($record->question) {
                return mb_substr($record->question, 0, 10).'..';
            }
        });
        $this->setFormatter('answer', function ($record) {
            if ($record->answer) {
                return mb_substr($record->answer, 0, 10).'..';
            }
        });
    }

    public function getCollection()
    {
        return parent::getCollection();
    }
}
