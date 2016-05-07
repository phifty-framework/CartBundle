<?php

namespace CartBundle\Model;

use LazyRecord\Schema\SchemaDeclare;

class OrderItemSchema extends SchemaDeclare
{
    public function schema()
    {
        $this->column('order_id')
            ->unsigned()
            ->integer()
            ->refer('CartBundle\\Model\\OrderSchema')
            ->label('訂單')
            ->renderable(false)
            ;

        if (kernel()->bundle('EventBundle')) {
            $this->column('event_reg_id')
                ->unsigned()
                ->integer()
                ->refer('EventBundle\\Model\\EventRegSchema')
                ->label('活動')
                ->renderable(false)
                ;
        }

        $this->column('quantity')
            ->integer()
            ->default(1)
            ->label('數量')
            ->renderAs('TextInput', ['size' => 2])
            ;

        $this->column('product_id')
            ->integer()
            ->refer('ProductBundle\\Model\\ProductSchema')
            ->required()
            ->label('產品')
            ;

        /*
        $this->column('type_id')
            ->unsigned()
            ->integer()
            ->refer('ProductBundle\\Model\\ProductTypeSchema')
            ->required()
            ->label('產品類型')
            ;
        */

        $this->column('remark')
            ->text()
            ->label('品項備註')
            ;

        if (kernel()->bundle('ShippingBundle')) {
            $this->mixin('ShippingBundle\\Model\\Mixin\\ShippingStatusMixinSchema');
            $this->belongsTo('shipping_company', 'ShippingBundle\\Model\\CompanySchema', 'id', 'shipping_company_id');
        }

        $this->belongsTo('order', 'CartBundle\\Model\\OrderSchema', 'id', 'order_id');

        $this->belongsTo('type', 'ProductBundle\\Model\\ProductTypeSchema', 'id', 'type_id');

        $this->belongsTo('product', 'ProductBundle\\Model\\ProductSchema', 'id', 'product_id');
    }
}
