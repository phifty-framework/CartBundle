<?php
namespace CartBundle\Action;
use ActionKit\Action;

class SubmitATM extends Action
{
    public function schema() {

        $this->param('order_id')
            ->required()
            ->label( _('訂單編號') )
            ;

        $this->param('order_token')
            ->required()
            ->label( _('訂單安全碼') )
            ;

        $this->param('bank_name')
            ->required()
            ->label( _('銀行') )
            ;
        $this->param('bank_code')
            ->required()
            ->label( _('銀行代碼') )
            ;

        $this->param('account_number')
            ->required()
            ->label( _('帳號末五碼') )
            ;

        $this->param('amount')
            ->required()
            ->label( _('金額') )
            ;

        $this->param('date')
            ->required()
            ->label( _('匯款日期') )
            ;
    }

    public function run() {
        return $this->success( _('謝謝您的購買，我們會請專人幫您查帳。') );
    }
}
