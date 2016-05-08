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

        $this->column('type_id')
            ->unsigned()
            ->integer()
            ->label('產品類型')
            ;

        $this->column('remark')
            ->text()
            ->label('品項備註')
            ;


        $this->column('logistics_id')
            ->unsigned()
            ->integer()
            ->label('物流公司')
            ->renderAs('SelectInput', [ 'allow_empty' => true, ])
            ;

        $this->column('delivery_number')
            ->varchar(64)
            ->label('物流編號')
            ->renderAs('TextInput', [ 'size' => 12 ])
            ;

        $this->column('delivery_status')
            ->varchar(32)
            ->label('貨運配送狀態')
            ->default('unpaid')
            ->validValues(array( 
                '未付款'       => 'unpaid',
                '確認中'       => 'confirming',
                '處理中'       => 'processing',
                '包裝中'       => 'packing',
                '已出貨'       => 'shipped',

                '缺貨中'       => 'stockout',


                '申請退貨'     => 'returning',
                '已經退貨'     => 'returned',
                // '已退貨'       => '',
            ))
            ;

        $this->column('return_reason')
            ->text()
            ->label( _('退貨原因') )
            ->renderAs('TextareaInput',[ 
                'rows' => 4,
                'cols' => 60,
            ])
            ;

        $this->column('delivery_status_last_updated_at')
            ->datetime()
            ->null()
            ->required()
            ->renderAs('DateTimeInput')
            ->label( _('貨運狀態更新時間') )
            ->default(function() {
                return date('c');
            })
            ;

        $this->belongsTo('logistics', 'CartBundle\\Model\\LogisticsSchema', 'id', 'logistics_id');

        $this->belongsTo('order', 'CartBundle\\Model\\OrderSchema', 'id', 'order_id');

        $this->belongsTo('type', 'ProductBundle\\Model\\ProductTypeSchema', 'id', 'type_id');

        $this->belongsTo('product', 'ProductBundle\\Model\\ProductSchema', 'id', 'product_id');
    }
}
