<?php
namespace CartBundle\Model;
use LazyRecord\Schema\SchemaDeclare;

class TransactionSchema extends SchemaDeclare
{
    public function schema() {
        $this->table('ftxs');

        $this->column('order_id')
            ->integer()
            ->required()
            ->label( _('訂單編號') )
            ->refer( 'CartBundle\\Model\\OrderSchema' )
            ;

        $this->column('status')
            ->varchar(32)
            ->label( _('交易狀態') )
            ->validValues([
                '交易成功' => 'success',
                '交易失敗' => 'fail'
            ]);
            ;

        $this->column('amount')
            ->integer()
            ->label( _('交易金額') )
            ;

        $this->column('result')
            ->boolean()
            ->default(false)
            ->label( _('交易結果') )
            ;

        $this->column('message')
            ->varchar(128)
            ->label( _('訊息') )
            ;

        $this->column('reason')
            ->varchar(128)
            ->label( _('原因') )
            ;

        $this->column('code')
            ->varchar(8)
            ->label( _('銀行回傳碼') )
            ;

        $this->column('data')
            ->text()
            ->label( _('描述交易資料') )
            ;

        $this->column('raw_data')
            ->text()
            ->label( _('原始 API 交易資料') )
            ;

        $this->belongsTo( 'order' , 'CartBundle\\Model\\OrderSchema', 'id', 'order_id');
    }
}
