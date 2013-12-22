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
            ->boolean()
            ->label( _('交易狀態') )
            ->default(false)
            ;

        $this->column('amount')
            ->integer()
            ->label( _('交易金額') )
            ;

        $this->column('code')
            ->label( _('銀行回傳碼') )
            ;

        $this->column('data')
            ->text()
            ->label( _('交易資料') )
            ;

        $this->belongsTo( 'order' , 'CartBundle\\Model\\OrderSchema', 'id', 'order_id');
    }
}
