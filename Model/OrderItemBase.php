<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
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
    public static $column_names = array (
      0 => 'id',
      1 => 'order_id',
      2 => 'event_reg_id',
      3 => 'quantity',
      4 => 'product_id',
      5 => 'type_id',
      6 => 'remark',
      7 => 'shipping_id',
      8 => 'shipping_company_id',
      9 => 'shipping_status',
      10 => 'returning_reason',
      11 => 'shipping_status_last_update',
    );
    public static $column_hash = array (
      'id' => 1,
      'order_id' => 1,
      'event_reg_id' => 1,
      'quantity' => 1,
      'product_id' => 1,
      'type_id' => 1,
      'remark' => 1,
      'shipping_id' => 1,
      'shipping_company_id' => 1,
      'shipping_status' => 1,
      'returning_reason' => 1,
      'shipping_status_last_update' => 1,
    );
    public static $mixin_classes = array (
      0 => 'ShippingBundle\\Model\\Mixin\\ShippingStatusMixinSchema',
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
    public function getEventRegId()
    {
            return $this->get('event_reg_id');
    }
    public function getQuantity()
    {
            return $this->get('quantity');
    }
    public function getProductId()
    {
            return $this->get('product_id');
    }
    public function getTypeId()
    {
            return $this->get('type_id');
    }
    public function getRemark()
    {
            return $this->get('remark');
    }
    public function getShippingId()
    {
            return $this->get('shipping_id');
    }
    public function getShippingCompanyId()
    {
            return $this->get('shipping_company_id');
    }
    public function getShippingStatus()
    {
            return $this->get('shipping_status');
    }
    public function getReturningReason()
    {
            return $this->get('returning_reason');
    }
    public function getShippingStatusLastUpdate()
    {
            return $this->get('shipping_status_last_update');
    }
}
