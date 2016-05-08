<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaDeclare;

class CouponSchema extends SchemaDeclare
{
    public function schema()
    {

        $this->column('brief')
            ->varchar(128)
            ->label('折價券簡述')
            ;

        $this->column('coupon_code')
            ->varchar(12)
            ->label('折價券號碼')
            ->required()
            ->default(function() {
                $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $code = "";
                $length = 6;
                for ($i = 0; $i < $length; $i++) {
                    $code .= $chars[mt_rand(0, strlen($chars)-1)];
                }
                return $code;
            })
            ;

        $this->column('use_limit')
            ->unsigned()
            ->integer()
            ->default(0)
            ->label('使用次數限制')
            ;

        $this->column('used')
            ->unsigned()
            ->integer()
            ->default(0)
            ->label('已使用次數')
            ;

        $this->column('required_amount')
            ->unsigned()
            ->integer()
            ->default(0)
            ->label('需總金額')
            ;

        $this->column('discount')
            ->unsigned()
            ->integer()
            ->default(0)
            ->label('折價金額')
            ;
    }

    public function bootstrap($record) {
        $record->create(array(
            'coupon_code' => 'XXX',
            'used' => 0,
            'discount' => 100,
            'use_limit' => 0,
        ));
    }
}
