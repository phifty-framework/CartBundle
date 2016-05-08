<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class LogisticsCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\LogisticsSchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\Logistics';
    const TABLE = 'logistics_companies';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
