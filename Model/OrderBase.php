<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaLoader;
use LazyRecord\Result;
use SQLBuilder\Bind;
use SQLBuilder\ArgumentArray;
use PDO;
use SQLBuilder\Universal\Query\InsertQuery;
use LazyRecord\BaseModel;
class OrderBase
    extends BaseModel
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\OrderSchemaProxy';
    const COLLECTION_CLASS = 'CartBundle\\Model\\OrderCollection';
    const MODEL_CLASS = 'CartBundle\\Model\\Order';
    const TABLE = 'orders';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
    const FIND_BY_PRIMARY_KEY_SQL = 'SELECT * FROM orders WHERE id = :id LIMIT 1';
    public static $column_names = array (
      0 => 'id',
      1 => 'buyer_name',
      2 => 'buyer_gender',
      3 => 'buyer_cellphone',
      4 => 'buyer_phone_area',
      5 => 'buyer_phone',
      6 => 'buyer_phone_ext',
      7 => 'buyer_postcode',
      8 => 'buyer_address',
      9 => 'shipping_name',
      10 => 'shipping_gender',
      11 => 'shipping_cellphone',
      12 => 'shipping_phone_area',
      13 => 'shipping_phone',
      14 => 'shipping_phone_ext',
      15 => 'shipping_postcode',
      16 => 'shipping_address',
      17 => 'sn',
      18 => 'token',
      19 => 'payment_type',
      20 => 'payment_status_last_update',
      21 => 'payment_status',
      22 => 'pod_time',
      23 => 'is_deleted',
      24 => 'coupon_code',
      25 => 'invoice_number',
      26 => 'invoice_type',
      27 => 'utc',
      28 => 'utc_title',
      29 => 'utc_address',
      30 => 'utc_name',
      31 => 'shipping_fee',
      32 => 'discount_amount',
      33 => 'paid_amount',
      34 => 'total_amount',
      35 => 'remark',
      36 => 'member_id',
      37 => 'event_id',
      38 => 'event_reg_id',
      39 => 'created_on',
      40 => 'updated_on',
      41 => 'created_by',
      42 => 'updated_by',
    );
    public static $column_hash = array (
      'id' => 1,
      'buyer_name' => 1,
      'buyer_gender' => 1,
      'buyer_cellphone' => 1,
      'buyer_phone_area' => 1,
      'buyer_phone' => 1,
      'buyer_phone_ext' => 1,
      'buyer_postcode' => 1,
      'buyer_address' => 1,
      'shipping_name' => 1,
      'shipping_gender' => 1,
      'shipping_cellphone' => 1,
      'shipping_phone_area' => 1,
      'shipping_phone' => 1,
      'shipping_phone_ext' => 1,
      'shipping_postcode' => 1,
      'shipping_address' => 1,
      'sn' => 1,
      'token' => 1,
      'payment_type' => 1,
      'payment_status_last_update' => 1,
      'payment_status' => 1,
      'pod_time' => 1,
      'is_deleted' => 1,
      'coupon_code' => 1,
      'invoice_number' => 1,
      'invoice_type' => 1,
      'utc' => 1,
      'utc_title' => 1,
      'utc_address' => 1,
      'utc_name' => 1,
      'shipping_fee' => 1,
      'discount_amount' => 1,
      'paid_amount' => 1,
      'total_amount' => 1,
      'remark' => 1,
      'member_id' => 1,
      'event_id' => 1,
      'event_reg_id' => 1,
      'created_on' => 1,
      'updated_on' => 1,
      'created_by' => 1,
      'updated_by' => 1,
    );
    public static $mixin_classes = array (
      0 => 'CommonBundle\\Model\\Mixin\\MetaSchema',
    );
    protected $table = 'orders';
    public $readSourceId = 'default';
    public $writeSourceId = 'default';
    public function getSchema()
    {
        if ($this->_schema) {
           return $this->_schema;
        }
        return $this->_schema = SchemaLoader::load('CartBundle\\Model\\OrderSchemaProxy');
    }
    public function getId()
    {
            return $this->get('id');
    }
    public function getBuyerName()
    {
            return $this->get('buyer_name');
    }
    public function getBuyerGender()
    {
            return $this->get('buyer_gender');
    }
    public function getBuyerCellphone()
    {
            return $this->get('buyer_cellphone');
    }
    public function getBuyerPhoneArea()
    {
            return $this->get('buyer_phone_area');
    }
    public function getBuyerPhone()
    {
            return $this->get('buyer_phone');
    }
    public function getBuyerPhoneExt()
    {
            return $this->get('buyer_phone_ext');
    }
    public function getBuyerPostcode()
    {
            return $this->get('buyer_postcode');
    }
    public function getBuyerAddress()
    {
            return $this->get('buyer_address');
    }
    public function getShippingName()
    {
            return $this->get('shipping_name');
    }
    public function getShippingGender()
    {
            return $this->get('shipping_gender');
    }
    public function getShippingCellphone()
    {
            return $this->get('shipping_cellphone');
    }
    public function getShippingPhoneArea()
    {
            return $this->get('shipping_phone_area');
    }
    public function getShippingPhone()
    {
            return $this->get('shipping_phone');
    }
    public function getShippingPhoneExt()
    {
            return $this->get('shipping_phone_ext');
    }
    public function getShippingPostcode()
    {
            return $this->get('shipping_postcode');
    }
    public function getShippingAddress()
    {
            return $this->get('shipping_address');
    }
    public function getSn()
    {
            return $this->get('sn');
    }
    public function getToken()
    {
            return $this->get('token');
    }
    public function getPaymentType()
    {
            return $this->get('payment_type');
    }
    public function getPaymentStatusLastUpdate()
    {
            return $this->get('payment_status_last_update');
    }
    public function getPaymentStatus()
    {
            return $this->get('payment_status');
    }
    public function getPodTime()
    {
            return $this->get('pod_time');
    }
    public function getIsDeleted()
    {
            return $this->get('is_deleted');
    }
    public function getCouponCode()
    {
            return $this->get('coupon_code');
    }
    public function getInvoiceNumber()
    {
            return $this->get('invoice_number');
    }
    public function getInvoiceType()
    {
            return $this->get('invoice_type');
    }
    public function getUtc()
    {
            return $this->get('utc');
    }
    public function getUtcTitle()
    {
            return $this->get('utc_title');
    }
    public function getUtcAddress()
    {
            return $this->get('utc_address');
    }
    public function getUtcName()
    {
            return $this->get('utc_name');
    }
    public function getShippingFee()
    {
            return $this->get('shipping_fee');
    }
    public function getDiscountAmount()
    {
            return $this->get('discount_amount');
    }
    public function getPaidAmount()
    {
            return $this->get('paid_amount');
    }
    public function getTotalAmount()
    {
            return $this->get('total_amount');
    }
    public function getRemark()
    {
            return $this->get('remark');
    }
    public function getMemberId()
    {
            return $this->get('member_id');
    }
    public function getEventId()
    {
            return $this->get('event_id');
    }
    public function getEventRegId()
    {
            return $this->get('event_reg_id');
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
