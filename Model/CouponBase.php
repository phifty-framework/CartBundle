<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class CouponBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\CouponSchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\CouponCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\Coupon';
    const TABLE = 'coupons';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM coupons WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'brief',
      2 => 'coupon_code',
      3 => 'use_limit',
      4 => 'used',
      5 => 'required_amount',
      6 => 'discount',
    );
    public static $column_hash = array (
      'id' => 1,
      'brief' => 1,
      'coupon_code' => 1,
      'use_limit' => 1,
      'used' => 1,
      'required_amount' => 1,
      'discount' => 1,
    );
    public static $mixin_classes = array (
    );
    protected $table = 'coupons';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\CouponSchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getBrief()
    {
            return $this->get('brief');
    }
    public function getCouponCode()
    {
            return $this->get('coupon_code');
    }
    public function getUseLimit()
    {
            return $this->get('use_limit');
    }
    public function getUsed()
    {
            return $this->get('used');
    }
    public function getRequiredAmount()
    {
            return $this->get('required_amount');
    }
    public function getDiscount()
    {
            return $this->get('discount');
    }
}
