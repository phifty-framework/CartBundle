<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class CouponCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\CouponSchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\Coupon';
    const TABLE = 'coupons';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
