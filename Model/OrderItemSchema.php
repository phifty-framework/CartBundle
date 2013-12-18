<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaDeclare;

class OrderItemSchema extends SchemaDeclare
{
    public function schema()
    {

        $this->column('order_id')
            ->integer()
            ->refer('CartBundle\\Model\\OrderSchema')
            ;

        $this->column('quantity')
            ->integer()
            ->default(1)
            ;

        $this->column('product_id')
            ->integer()
            ->refer('ProductBundle\\Model\\ProductSchema')
            ->required()
            ;

        $this->column('type_id')
            ->integer()
            ->refer('ProductBundle\\Model\\ProductTypeSchema')
            ->required()
            ;


    }
}
