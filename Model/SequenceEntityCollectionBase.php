<?php
namespace CartBundle\Model;
use LazyRecord\BaseCollection;
class SequenceEntityCollectionBase
    extends BaseCollection
{
    const SCHEMA_PROXY_CLASS = 'CartBundle\\Model\\SequenceEntitySchemaProxy';
    const MODEL_CLASS = 'CartBundle\\Model\\SequenceEntity';
    const TABLE = 'sequence_entities';
    const READ_SOURCE_ID = 'default';
    const WRITE_SOURCE_ID = 'default';
    const PRIMARY_KEY = 'id';
}
