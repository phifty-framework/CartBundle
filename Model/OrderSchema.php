<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaDeclare;
use MemberBundle\CurrentMember;

class OrderSchema extends SchemaDeclare
{

    public function schema()
    {
        $bundle = kernel()->bundle('CartBundle');
        $prefixes = [ '訂購人' => 'buyer_', '收件人' => 'shipping_'];
        foreach ($prefixes as $label => $prefix) {
            $this->column("{$prefix}name")
                ->varchar(32)
                ->label( "{$label}姓名" )
                ->renderAs('TextInput', [ 'size' => 8 ])
                ->required()
                ;

            $this->column("{$prefix}gender")
                ->varchar(32)
                ->label( "{$label}性別" )
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
                ->label( "{$label}手機" )
                ->required()
                ->renderAs('TextInput', [ 'size' => 12 ])
                ;

            $this->column("{$prefix}phone_area")
                ->varchar(3)
                ->label("{$label}區碼")
                ->renderAs('TextInput', [ 'size' => 3 ])
                ;

            $this->column("{$prefix}phone")
                ->varchar(32)
                ->label("{$label}電話")
                ->renderAs('TextInput', [ 'size' => 8 ])
                ;

            $this->column("{$prefix}phone_ext")
                ->varchar(3)
                ->label("{$label}分機")
                ->renderAs('TextInput', [ 'size' => 3 ])
                ;

            $this->column("{$prefix}postcode")
                ->varchar(6)
                ->label("{$label}郵遞區號")
                ->renderAs('TextInput', [ 'size' => 3 ])
                ;

            $this->column("{$prefix}address")
                ->varchar(128)
                ->label( "{$label}地址" )
                ->required()
                ->renderAs('TextInput', [ 'size' => 60 ])
                ;
        }

        $this->column('sn')
            ->varchar(16)
            ->label( _('訂單編號') )
            ->renderAs('TextInput', [ 'size' => 10 ])
            ;

        // Order Security Token (to access from URL)
        $this->column('token')
            ->varchar(32)
            ->label('訂單 Security Token')
            ->hint('請勿修改或清空')
            ->renderAs('TextInput', [ 'size' => 8 ])
            ->default(function() {
                return substr(md5(uniqid('', true)),0,8);
            })
            ;


        if ( $bundle->config('ChooseDeliveryType') ) {
        $this->column('delivery_type')
            ->varchar(32)
            ->label( _('取貨方式') )
            ->validValues([
                '宅配' => 'home',
                '到店取貨' => 'store',
            ])
            ->renderAs('SelectInput')
            ;

        $this->column('delivery_store')
            ->integer()
            ->label( _('取貨店家') )
            ->validValues(function() {
                $c = new \StoreLocationBundle\Model\StoreCategory(array( 'handle' => 'delivery' ));
                if ( $c->id ) {
                    return $c->stores->asPairs('title','id');
                }
            })
            ->renderAs('SelectInput',[ 'allow_empty' => true ])
            ;
        }

        // we keep this field for admin to query items easily.
        // and "POD" won't have "transaction record" before the shipping is done.
        $this->column('payment_type')
            ->varchar(32)
            ->label( _('付款方式') )
            ->validValues([
                '貨到付款' => 'pod', // pay on delivery
                'ATM'      => 'atm',
                '信用卡'   => 'cc', // credit card
            ])
            ;

        $this->column('payment_status_last_update')
            ->timestamp()
            ->null()
            ->renderAs('DateTimeInput')
            ->label( _('付款狀態最後更新時間') )
            ->default(function() {
                return date('c');
            })
            ;

        $this->column('payment_status')
            ->varchar(32)
            ->default(function() { 
                return 'unpaid'; 
            })
            ->label( _('付款狀態') )
            ->validValues([
                '未付款'         => 'unpaid',
                '付款失敗'       => 'paid_error',
                '已付款'         => 'paid',
                '已付款但費用不足' => 'paid_incomplete',

                // used in ATM
                '已付款待確認'   => 'confirming',
            ]);

        $this->column('pod_time')
            ->varchar(24)
            ->null()
            ->label( '貨到付款到貨時間' )
            ->validValues([
                '不限' => '0',
                '上午9:00 ~ 上午12:00'  => '0900-1200',
                '上午12:00 ~ 下午15:00' => '1200-1500',
                '下午15:00 ~ 下午18:00' => '1500-1800',
                '下午18:00 ~ 晚上21:00' => '1800-2100',
            ])
            ;

        $this->column('is_deleted')
            ->boolean()
            ->label('已刪除')
            ->default(false)
            ->renderAs('CheckboxInput')
            ;

        $this->column('coupon_code')
            ->varchar(32)
            ->label( _('使用的折價券代碼') )
            ->renderAs('TextInput', [ 'size' => 8 ])
            ;

        $this->column('invoice_number')
            ->varchar(32)
            ->label('發票編號')
            ->renderAs('TextInput', [ 'size' => 12 ])
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
            ->renderAs('TextInput', [ 'size' => 8 ])
            ;

        $this->column('utc_title')
            ->varchar(64)
            ->label('發票抬頭')
            ->renderAs('TextInput', [ 'size' => 12 ])
            ;

        $this->column('utc_address')
            ->varchar(128)
            ->label('發票寄送地址')
            ->renderAs('TextInput', [ 'size' => 30 ])
            ;

        $this->column('utc_name')
            ->varchar(32)
            ->label('發票收件人')
            ->renderAs('TextInput', [ 'size' => 8 ])
            ;

        $this->column('shipping_cost')
            ->integer()
            ->default(0)
            ->label('運費')
            ->renderAs('TextInput', [ 'size' => 5 ])
            ;

        $this->column('discount_amount')
            ->integer()
            ->default(0)
            ->label('折扣金額')
            ->renderAs('TextInput', [ 'size' => 5 ])
            ;

        $this->column('paid_amount')
            ->integer()
            ->default(0)
            ->label('已付金額')
            ->renderAs('TextInput', [ 'size' => 6 ])
            ;

        $this->column('total_amount')
            ->integer()
            ->default(0)
            ->label('總金額')
            ->renderAs('TextInput', [ 'size' => 6 ])
            ;

        $this->column('remark')
            ->text()
            ->label('消費者備註')
            ->renderAs('TextareaInput', [ 'rows' => 1, 'cols' => 40 ]) 
            ;

        // an order has many order items
        $this->many( 'order_items', 'CartBundle\\Model\\OrderItemSchema', 'order_id', 'id' );

        $this->many( 'transactions' , 'CartBundle\\Model\\TransactionSchema', 'order_id', 'id')
            ->ordering('id','desc')
            ;


        $this->column( 'member_id' )
            ->integer()
            ->refer('MemberBundle\\Model\\MemberSchema')
            ->immutable()
            ->default(function() {
                if ( isset($_SESSION) ) {
                    $currentMember = new \MemberBundle\CurrentMember;
                    return $currentMember->id;
                }
            })
            ->renderAs('SelectInput')
            ->label('會員')
            ;

        $this->belongsTo('member', 'MemberBundle\\Model\\MemberSchema', 'id', 'member_id');

        $this->mixin('CommonBundle\\Model\\Mixin\\MetaSchema');
    }
}
