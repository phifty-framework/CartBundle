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
            ->label('訂單')
            ;

        $this->column('quantity')
            ->integer()
            ->default(1)
            ->label('數量')
            ;

        $this->column('product_id')
            ->integer()
            ->refer('ProductBundle\\Model\\ProductSchema')
            ->required()
            ->label('產品')
            ;

        $this->column('type_id')
            ->integer()
            ->refer('ProductBundle\\Model\\ProductTypeSchema')
            ->required()
            ->label('產品類型')
            ;

        $this->column('shipping_id')
            ->varchar(64)
            ->label('物流編號')
            ;

        if ( kernel()->bundle('ShippingBundle') ) {
        $this->column('shipping_company_id')
            ->integer()
            ->label('物流公司')
            ->refer('ShippingBundle\\Model\\CompanySchema')
            ;
        }

        $this->column('shipping_status')
            ->varchar(32)
            ->label('物品寄送狀態')
            ->default('unpaid')
            ->validValues(array( 
                '未付款' => 'unpaid',
                '處理中' => 'processing',
                '寄送中' => 'transfering',
                '已到貨' => 'completed',
            ))
            ;

        if ( kernel()->bundle('ShippingBundle') ) {
            $this->belongsTo('shipping_company', 'ShippingBundle\\Model\\CompanySchema', 'id', 'shipping_company_id' );
        }

        $this->belongsTo('order', 'CartBundle\\Model\\OrderSchema','id','order_id');

        $this->belongsTo('type', 'ProductBundle\\Model\\ProductTypeSchema','id','type_id');

        $this->belongsTo('product', 'ProductBundle\\Model\\ProductSchema','id','product_id');

    }
}
