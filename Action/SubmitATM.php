<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\CreateRecordAction;
use CartBundle\Model\Transaction;
use CartBundle\Model\Order;
use DateTime;
use CartBundle\Email\PaymentATMEmail;
use CartBundle\Email\AdminOrderPaymentEmail;

class SubmitATM extends CreateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\Transaction';




    public function schema() {

        $this->param('o')
            ->required()
            ->label( _('訂單編號') )
            ;

        $this->param('t')
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

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function run() {
        $order = new Order;
        $order->load([ 'id' => $this->arg('o'), 'token' => $this->arg('t') ]);
        if ( ! $order->id ) {
            return $this->error( _('參數錯誤') );
        }

        if ( $this->arg('amount') < $order->total_amount ) {
            return $this->error( _('匯款金額不正確喔，請再確認一下。') );
        }

        $date = $this->arg('date');
        // validate date
        if ( ! $this->validateDate($date,'Y-m-d') ) {
            return $this->error( _('日期不正確喔') );
        }
        // the correct date range should be   "order.created_on" < ATM < before tomorrow
        $paidDate = new DateTime($date);
        $now      = new DateTime;
        if ( $paidDate > $now ) {
            return $this->error( _('日期不正確喔。') );
        }

        $orderCreatedTime = new DateTime($order->created_on);
        if ( $paidDate < $orderCreatedTime->format('Y-m-d') ) {
            return $this->error( _('日期過早不正確喔') );
        }


        $txn = new Transaction;
        $ret = $txn->create([
            'result' => true,
            'order_id' => $order->id,
            'type' => 'atm',
            'data' => yaml_emit([
                '帳號末五碼' => $this->arg('account_number'),
                '銀行名稱' => $this->arg('bank_name'),
                '銀行代號' => $this->arg('bank_code'),
            ], YAML_UTF8_ENCODING),
            'message'     => '已建立 ATM 匯款記錄',
            'reason'      => '客戶已提交 ATM 匯款資料，請確認',
            'amount'      => $this->arg('amount'),
            'paid_date'   => $this->arg('date'),
        ]);
        if ( $ret->success ) {
            $email = new PaymentATMEmail($order->member, $order);
            $email->send();

            $adminEmail = new AdminOrderPaymentEmail($order->member, $order, $txn);
            $adminEmail->send();

            return $this->success( _('謝謝您的購買，我們會請專人幫您處理。') );
        } else {
            $this->convertRecordValidation($ret);
            return $this->error( __('錯誤 %1', $ret->message ) );
        }
    }
}
