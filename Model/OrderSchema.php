<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaDeclare;
use MemberBundle\CurrentMember;

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

        $this->column('token')
            ->varchar(32)
            ->label('訂單 Security Token')
            ->hint('請勿修改或清空')
            ->default(function() {
                return substr(md5(uniqid('', true)),0,8);
            })
            ;

        $this->column('payment_type')
            ->varchar(32)
            ->validValues([
                '貨到付款' => 'pod', // pay on delivery
                'ATM'      => 'atm',
                '信用卡'   => 'cc', // credit card
            ])
            ;

        $this->column('payment_status')
            ->varchar(32)
            ->default(function() { 
                return 'unpaid'; 
            })
            ->validValues([
                '未付款'         => 'unpaid',
                '付款失敗'       => 'paid_error',
                '已付款'         => 'paid',
                '已付款但費用不足' => 'paid_incomplete',

                // used in ATM
                '已付款確認中'         => 'paid_confirming'
            ]);

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
            ->label('統一編號')
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

        $this->column('shipping_cost')
            ->integer()
            ->default(0)
            ->label('運費')
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


        // an order has many order items
        $this->many( 'order_items', 'CartBundle\\Model\\OrderItemSchema', 'order_id', 'id' );

        $this->many( 'transactions' , 'CartBundle\\Model\\TransactionSchema', 'order_id', 'id');


        $this->column( 'member_id' )
            ->integer()
            ->refer('MemberBundle\\Model\\MemberSchema')
            ->default(function() {
                if ( isset($_SESSION) ) {
                    $currentMember = new \MemberBundle\CurrentMember;
                    return $currentMember->id;
                }
            })
            ->renderAs('SelectInput')
            ->label('會員')
            ;

        $this->mixin('CommonBundle\\Model\\Mixin\\MetaSchema');
    }
}
