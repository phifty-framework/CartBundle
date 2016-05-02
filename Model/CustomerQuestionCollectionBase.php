<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class CustomerQuestionCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\CustomerQuestionSchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\CustomerQuestion';
    const TABLE = 'customer_questions';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
