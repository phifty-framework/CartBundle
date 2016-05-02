<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class TransactionCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\TransactionSchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\Transaction';
    const TABLE = 'ftxs';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
