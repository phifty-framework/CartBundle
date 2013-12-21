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
                ->label( "$label 姓名" )
                ->required()
                ;

            $this->column("{$prefix}gender")
                ->varchar(32)
                ->label( "$label 性別" )
                ->required()
                ->validValues([
                    /**
                     * Gentleman, ladies
                     */
                    '先生' => 'male',
                    '小姐' => 'female',
                ])
                ;

            $this->column("{$prefix}cellphone")
                ->varchar(32)
                ->label( "$label 手機" )
                ->required()
                ;
            $this->column("{$prefix}phone")
                ->varchar(32)
                ->label( "$label 聯絡電話" )
                ;
            $this->column("{$prefix}address")
                ->varchar(128)
                ->label( "$label 地址" )
                ->required()
                ;
        }

        $this->column('invoice_number')
            ->varchar(32)
            ->label('發票編號')
            ;

        $this->column('invoice_type')
            ->integer()
            ->validValues([
                '二聯' => 2,
                '三聯' => 3,
            ])
            ->label('發票種類')
            ->renderAs('SelectInput')
            ;

        // unified taxation code
        $this->column('utc')
            ->varchar(12)
            ->label('統編抬頭')
            ;

        $this->column('utc_title')
            ->varchar(64)
            ->label('統編抬頭')
            ;

        $this->column('utc_address')
            ->varchar(128)
            ->label('發票寄送地址')
            ;

        $this->column('utc_name')
            ->varchar(32)
            ->label('發票收件人')
            ;

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

        $this->column('remark')
            ->text()
            ->label('消費者備註')
            ;

    }
}
