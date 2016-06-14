<?php
namespace CartBundle\Model;

use LazyRecord\Schema\DeclareSchema;

class SequenceEntitySchema extends DeclareSchema
{
    public function schema()
    {
        $this->column('handle')
            ->varchar(20)
            ;

        $this->column('prefix')
            ->varchar(20)
            ;

        $this->column('pad_char')
            ->char(1)
            ->default('0')
            ;

        $this->column('pad_length')
            ->unsigned()
            ->integer()
            ->default(0)
            ;

        $this->column('start_id')
            ->unsigned()
            ->integer()
            ->default(1)
            ;

        $this->column('last_id')
            ->unsigned()
            ->integer()
            ->default(1)
            ;

        $this->column('increment')
            ->integer()
            ->unsigned()
            ->default(1)
            ;

        $this->mixin('CommonBundle\\Model\\Mixin\\MetaSchema');
    }
}

