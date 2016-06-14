<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class CustomerQuestionBase
    extends BaseModel
{
    const SCHEMA_CLASS = 'CartBundle\\Model\\CustomerQuestionSchema';
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\CustomerQuestionSchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\CustomerQuestionCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\CustomerQuestion';
    const TABLE = 'customer_questions';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM customer_questions WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'question_title',
      2 => 'question',
      3 => 'question_time',
      4 => 'answer',
      5 => 'answer_time',
      6 => 'order_id',
      7 => 'order_item_id',
      8 => 'member_id',
      9 => 'remark',
      10 => 'created_on',
      11 => 'updated_on',
      12 => 'created_by',
      13 => 'updated_by',
    );
    public static $column_hash = array (
      'id' => 1,
      'question_title' => 1,
      'question' => 1,
      'question_time' => 1,
      'answer' => 1,
      'answer_time' => 1,
      'order_id' => 1,
      'order_item_id' => 1,
      'member_id' => 1,
      'remark' => 1,
      'created_on' => 1,
      'updated_on' => 1,
      'created_by' => 1,
      'updated_by' => 1,
    );
    public static $mixin_classes = array (
      0 => 'CommonBundle\\Model\\Mixin\\MetaSchema',
    );
    protected $table = 'customer_questions';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\CustomerQuestionSchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getQuestionTitle()
    {
            return $this->get('question_title');
    }
    public function getQuestion()
    {
            return $this->get('question');
    }
    public function getQuestionTime()
    {
            return $this->get('question_time');
    }
    public function getAnswer()
    {
            return $this->get('answer');
    }
    public function getAnswerTime()
    {
            return $this->get('answer_time');
    }
    public function getOrderId()
    {
            return $this->get('order_id');
    }
    public function getOrderItemId()
    {
            return $this->get('order_item_id');
    }
    public function getMemberId()
    {
            return $this->get('member_id');
    }
    public function getRemark()
    {
            return $this->get('remark');
    }
    public function getCreatedOn()
    {
            return $this->get('created_on');
    }
    public function getUpdatedOn()
    {
            return $this->get('updated_on');
    }
    public function getCreatedBy()
    {
            return $this->get('created_by');
    }
    public function getUpdatedBy()
    {
            return $this->get('updated_by');
    }
}
