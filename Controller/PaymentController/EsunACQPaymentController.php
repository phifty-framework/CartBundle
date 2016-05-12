<?php

namespace CartBundle\Controller\PaymentController;

use CartBundle\CartBundle;
use CartBundle\Model\Order;
use CartBundle\Model\Transaction;
use CartBundle\Controller\OrderBaseController;
use CartBundle\Email\PaymentCreditCardEmail;
use CartBundle\Email\AdminOrderPaymentEmail;
use Exception;

use EsunBank\ACQ\AuthRequestBuilder;
use EsunBank\ACQ\AuthResponseVerifier;
use EsunBank\ACQ\TxnType;
use EsunBank\ACQ\AuthReturnCode;



class EsunACQPaymentController extends BasePaymentController implements ThirdPartyPaymentController
{
    public function getPaymentId()
    {
        return 'esunacq';
    }

    public function buildFormFields(Order $order, array $override = array())
    {
        $MID = $this->getPaymentConfig('MID');
        $CID = $this->getPaymentConfig('CID') ?: '';
        $KEY = $this->getPaymentConfig('KEY'); // MAC KEY
        $ONO = sprintf('%s%02d',$order->sn, $order->transactions->size());
        $TA = $order->total_amount;
        $returnUrl = $this->getReturnUrl();
        $builder = new AuthRequestBuilder($KEY, [
            'MID' => $MID,  // 特店代碼 char(15)
            'CID' => $CID,  // 子特店代碼 char(20)
            'U'   => $returnUrl ?: '/TestACQ/print.html',
        ]);
        return array_merge($builder->formFields($ONO, $TA), $override);
    }

    public function translateResponseFields(array $params)
    {
        $labels = [
            'RC'       => '回覆碼',
            'MID'      => '特店代碼',
            'ONO'      => '訂單編號',
            'TRANSNUM' => '交易序號',
            'VERSION'  => '版本',
            'LTD'      => '收單交易日期',
            'LTT'      => '收單交易時間',
            'RRN'      => '簽帳單序號',
            'AIR'      => '授權碼',

            // 分期付款資料欄位
            'ITA'  => '分期總金額',
            'IP'   => '分期期數',
            'IFPA' => '頭期款金額',
            'IPA'  => '每期金額',

            // 銀行紅利欄位
            'BRP' => '折抵點數',
            'BB'  => '剩餘點數',
            'BRA' => '折抵金額',
        ];
        $array = [];
        foreach ($params as $name => $value) {
            if (isset($labels[$name])) {
                $array[$labels[$name]] = $value;
            } else {
                $array[$name] = $value;
            }
        }
        // '回傳訊息' = $this->translateMessage($finalReturn_PRC, $finalReturn_SRC)
        return $array;
    }



    /*
    收單交易時間：195305
    M：f384d0d9b597f444a6c133148df0ee96
    簽帳單序號：086133000015
    授權碼：522741
    回覆碼：00
    特店代碼：8089014159
    AN：552199******1898
    訂單編號：TEST1463053932
    收單交易日期：20160512
    */
    public function returnAction()
    {
        // https://acqtest.esunbank.com.tw/TestACQ/print.html
        // RC=00&MID=8089014159&ONO=TEST1463053932&LTD=20160512&LTT=195305&RRN=086133000015&AIR=522741&AN=552199******1898&M=f384d0d9b597f444a6c133148df0ee96
        $verifier = new AuthResponseVerifier($this->getPaymentConfig('KEY'), [
            'MID' => $this->getPaymentConfig('MID'),
            'CID' => $this->getPaymentConfig('CID') ?: '',
        ]);

        $request = $this->getRequest();
        $returnCode = $request->param('RC');
        $result = true;
        $message = '交易成功';

        if ($returnCode != '00') {
            $result = false;
            $message = AuthReturnCode::getMessage($request->param('RC'));
        } else if (false === $verifier->verify($request->getQueryParameters())) {
            // the response is coming from query string ...
            $result = false;
            $message = "交易失敗";
        }

        $desc = $this->translateResponseFields($request->getQueryParameters());

        $orderSN = $request->param('ONO');
        $orderSN = substr($orderSN, 0, -2); // the last two number are txn no
        $order = new Order;
        try {
            $ret = $order->load(['sn' => $orderSN]);
            if ($ret->error || !$order->id) {
                throw new Exception('無此訂單');
            }
            $order->update(['payment_type' => 'cc']); // credit card

            $txn = new Transaction;
            $ret = $txn->create([
                'order_id' => $order->id,
                'type'     => 'cc',
                'result'   => $result,

                // esunbank doesn't send back the total amount,
                // we have to be very careful.
                'amount'   => intval($order->total_amount),
                'message'  => $message,
                'reason'   => $message,
                'code'     => $returnCode,
                'data'     => $this->_encode($desc),
                'raw_data' => $this->_encode($request->getParameters()),
            ]);
            if ($ret->error) {
                throw new Exception($ret->message);
            }
        } catch (Exception $e) {
            error_log($message = $e->getMessage());
        }
        if ($result) {
            $email = new PaymentCreditCardEmail($order->member, $order);
            $email->send();
            $adminEmail = new AdminOrderPaymentEmail($order->member, $order, $txn);
            $adminEmail->send();
        }
        return $this->render('message.html', [
            'error'   => !$result,
            'title'   => $message,
            'message' => $message,
        ]);
    }

}
