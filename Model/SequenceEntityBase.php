<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class SequenceEntityBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\SequenceEntitySchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\SequenceEntityCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\SequenceEntity';
    const TABLE = 'sequence_entities';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM sequence_entities WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'handle',
      2 => 'prefix',
      3 => 'pad_char',
      4 => 'pad_length',
      5 => 'start_id',
      6 => 'last_id',
      7 => 'increment',
      8 => 'created_on',
      9 => 'updated_on',
      10 => 'created_by',
      11 => 'updated_by',
    );
    public static $column_hash = array (
      'id' => 1,
      'handle' => 1,
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
    protected $table = 'sequence_entities';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\SequenceEntitySchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getHandle()
    {
            return $this->get('handle');
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
