<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class IncrementEntityBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\IncrementEntitySchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\IncrementEntityCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\IncrementEntity';
    const TABLE = 'increment_entities';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM increment_entities WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'prefix',
      2 => 'pad_char',
      3 => 'pad_length',
      4 => 'start_id',
      5 => 'last_id',
      6 => 'increment',
      7 => 'created_on',
      8 => 'updated_on',
      9 => 'created_by',
      10 => 'updated_by',
    );
    public static $column_hash = array (
      'id' => 1,
      'prefix' => 1,
      'pad_char' => 1,
      'pad_length' => 1,
      'start_id' => 1,
      'last_id' => 1,
      'increment' => 1,
      'created_on' => 1,
      'updated_on' => 1,
      'created_by' => 1,
      'updated_by' => 1,
    );
    public static $mixin_classes = array (
      0 => 'CommonBundle\\Model\\Mixin\\MetaSchema',
    );
    protected $table = 'increment_entities';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\IncrementEntitySchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getPrefix()
    {
            return $this->get('prefix');
    }
    public function getPadChar()
    {
            return $this->get('pad_char');
    }
    public function getPadLength()
    {
            return $this->get('pad_length');
    }
    public function getStartId()
    {
            return $this->get('start_id');
    }
    public function getLastId()
    {
            return $this->get('last_id');
    }
    public function getIncrement()
    {
            return $this->get('increment');
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
