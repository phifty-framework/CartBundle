<?php

namespace CartBundle\Model;

use LazyRecord\Schema\SchemaLoader;
use LazyRecord\BaseModel;

class OrderItemBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\OrderItemSchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\OrderItemCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\OrderItem';
    const TABLE = 'order_items';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM order_items WHERE id = :id LIMIT 1';
    public static $column_names = array(
      0 => 'id',
      1 => 'order_id',
      2 => 'quantity',
      3 => 'product_id',
      4 => 'remark',
    );
    public static $column_hash = array(
      'id' => 1,
      'order_id' => 1,
      'quantity' => 1,
      'product_id' => 1,
      'remark' => 1,
    );
    public static $mixin_classes = array(
    );
    protected $table = 'order_items';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
            return $this->_schema;
        }

        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\OrderItemSchemaProxy');
    }
    public function getId()
    {
        return $this->get('id');
    }
    public function getOrderId()
    {
        return $this->get('order_id');
    }
    public function getQuantity()
    {
        return $this->get('quantity');
    }
    public function getProductId()
    {
        return $this->get('product_id');
    }
    public function getRemark()
    {
        return $this->get('remark');
    }
}
