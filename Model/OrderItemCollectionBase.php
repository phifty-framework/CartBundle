<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class OrderItemCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\OrderItemSchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\OrderItem';
    const TABLE = 'order_items';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
