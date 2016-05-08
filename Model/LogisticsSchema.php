<?php
namespace CartBundle\Model;
use LazyRecord\Schema\DeclareSchema;

class LogisticsSchema extends DeclareSchema
{
    public function schema() 
    {
        $this->table('logistics_companies');

        $this->column('name')
            ->varchar(32)
            ->label('物流公司名稱')
            ;

        // Maybe different
        $this->column('shipping_fee')
            ->unsigned()
            ->integer()
            ->default(80)
            ->label('運費')
            ;

        $this->column('website')
            ->varchar(128)
            ->label('物流公司網站')
            ;

        $this->column('phone')
            ->varchar(32)
            ->label('聯絡電話')
            ;

        $this->column('handle')
            ->varchar(32)
            // ->renderable(false)
            ->immutable()
            ->label('追蹤用代碼')
            ;

        $this->column('remark')
            ->text()
            ->renderAs('TextareaInput', [ 'placeholder' => _('備註') ])
            ->label('備註')
            ;
    }

    public function bootstrap($record)
    {
        $record->loadOrCreate(array(
            'name' => '黑貓',
            'shipping_fee' => 80,
            'handle' => 't-cat.com.tw',
        ), 'handle');

        $record->loadOrCreate(array(
            'name' => '中華郵政',
            'shipping_fee' => 80,
            'handle' => 'post.gov.tw',
        ), 'handle');

        $record->loadOrCreate(array(
            'name' => '新竹貨運',
            'shipping_fee' => 80,
            'handle' => 'hct.com.tw',
        ) , 'handle');

        $record->loadOrCreate(array(
            'name' => '預設',
            'shipping_fee' => 80,
            'handle' => 'default',
        ), 'handle');
    }
}
