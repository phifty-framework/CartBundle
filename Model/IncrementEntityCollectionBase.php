<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class IncrementEntityCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\IncrementEntitySchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\IncrementEntity';
    const TABLE = 'increment_entities';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
