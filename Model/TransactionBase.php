<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class TransactionBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\TransactionSchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\TransactionCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\Transaction';
    const TABLE = 'ftxs';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM ftxs WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'order_id',
      2 => 'type',
      3 => 'amount',
      4 => 'result',
      5 => 'message',
      6 => 'reason',
      7 => 'code',
      8 => 'data',
      9 => 'raw_data',
      10 => 'paid_date',
      11 => 'created_on',
    );
    public static $column_hash = array (
      'id' => 1,
      'order_id' => 1,
      'type' => 1,
      'amount' => 1,
      'result' => 1,
      'message' => 1,
      'reason' => 1,
      'code' => 1,
      'data' => 1,
      'raw_data' => 1,
      'paid_date' => 1,
      'created_on' => 1,
    );
    public static $mixin_classes = array (
    );
    protected $table = 'ftxs';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\TransactionSchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getOrderId()
    {
            return $this->get('order_id');
    }
    public function getType()
    {
            return $this->get('type');
    }
    public function getAmount()
    {
            return $this->get('amount');
    }
    public function getResult()
    {
            return $this->get('result');
    }
    public function getMessage()
    {
            return $this->get('message');
    }
    public function getReason()
    {
            return $this->get('reason');
    }
    public function getCode()
    {
            return $this->get('code');
    }
    public function getData()
    {
            return $this->get('data');
    }
    public function getRawData()
    {
            return $this->get('raw_data');
    }
    public function getPaidDate()
    {
            return $this->get('paid_date');
    }
    public function getCreatedOn()
    {
            return $this->get('created_on');
    }
}
