<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaDeclare;

class OrderSchema extends SchemaDeclare
{
    public function schema() {
        $prefixes = [ 
            '寄件者' => 'sender_', 
            '收件者' => 'shipping_',
        ];
        foreach( $prefixes as $label => $prefix ) {

            $this->column("{$prefix}name")
                ->varchar(32)
                ->label( "$label 姓名" );

            $this->column("{$prefix}phone")
                ->varchar(32)
                ->label( "$label 聯絡電話" )
                ;
            $this->column("{$prefix}cellphone")
                ->varchar(32)
                ->label( "$label 手機" )
                ;
            $this->column("{$prefix}address")
                ->varchar(128)
                ->label( "$label 地址" )
                ;
        }

        $this->column('paid_amount')
            ->integer()
            ->default(0)
            ->label('已付金額')
            ;

        $this->column('total_amount')
            ->integer()
            ->default(0)
            ->label('總金額')
            ;

    }
}
