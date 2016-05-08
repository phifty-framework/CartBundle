<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class LogisticsBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\LogisticsSchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\LogisticsCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\Logistics';
    const TABLE = 'logistics_companies';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM logistics_companies WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'name',
      2 => 'shipping_fee',
      3 => 'website',
      4 => 'phone',
      5 => 'handle',
      6 => 'remark',
    );
    public static $column_hash = array (
      'id' => 1,
      'name' => 1,
      'shipping_fee' => 1,
      'website' => 1,
      'phone' => 1,
      'handle' => 1,
      'remark' => 1,
    );
    public static $mixin_classes = array (
    );
    protected $table = 'logistics_companies';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\LogisticsSchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getName()
    {
            return $this->get('name');
    }
    public function getShippingFee()
    {
            return $this->get('shipping_fee');
    }
    public function getWebsite()
    {
            return $this->get('website');
    }
    public function getPhone()
    {
            return $this->get('phone');
    }
    public function getHandle()
    {
            return $this->get('handle');
    }
    public function getRemark()
    {
            return $this->get('remark');
    }
}
