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
    const SCHEMA_CLASS = 'CartBundle\\Model\\OrderItemSchema';
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
      7 => 'logistics_id',
      8 => 'delivery_number',
      9 => 'delivery_status',
      10 => 'return_reason',
      11 => 'delivery_status_last_updated_at',
    );
    public static $column_hash = array (
      'id' => 1,
      'order_id' => 1,
      'event_reg_id' => 1,
      'quantity' => 1,
      'product_id' => 1,
      'type_id' => 1,
      'remark' => 1,
      'logistics_id' => 1,
      'delivery_number' => 1,
      'delivery_status' => 1,
      'return_reason' => 1,
      'delivery_status_last_updated_at' => 1,
    );
    public static $mixin_classes = array (
      0 => 'EventBundle\\Model\\Mixin\\EventRegOwnerMixinSchema',
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
    public function getLogisticsId()
    {
            return $this->get('logistics_id');
    }
    public function getDeliveryNumber()
    {
            return $this->get('delivery_number');
    }
    public function getDeliveryStatus()
    {
            return $this->get('delivery_status');
    }
    public function getReturnReason()
    {
            return $this->get('return_reason');
    }
    public function getDeliveryStatusLastUpdatedAt()
    {
            return $this->get('delivery_status_last_updated_at');
    }
}
