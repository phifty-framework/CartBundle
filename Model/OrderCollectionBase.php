<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class OrderCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\OrderSchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\Order';
    const TABLE = 'orders';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
