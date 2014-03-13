<?php
namespace CartBundle\Controller\PaymentController;
use Phifty\Controller;
use CartBundle\Model\Order;
use CartBundle\Model\Transaction;
use CartBundle\Controller\OrderBaseController;
use Exception;
use CartBundle\Email\PaymentCreditCardEmail;

class NewebPaymentController extends OrderBaseController
{

    /**
     * Translate response code to message for customers.
     *
     * @param string $PRC
     * @param string $SRC
     */
    public function translateCustomerMessage($PRC, $SRC)
    {
        $table = [
            '15-1018' => '系統無法處理。銀行主機忙碌中或銀行線路中斷',
            '34-171'  => '金融交易失敗',
            '8-204'   => '訂單編號重複',
            '52-554'  => '使用者帳號密碼錯誤',
        ];
        if ( isset($table["$PRC-$SRC"]) ) {
            return $table["$PRC-$SRC"];
        } else {
            return '請與商家聯絡';
        }
    }


    /**
     * @param string $PRC
     * @param string $SRC
     */
    public function translateMessage($PRC, $SRC) 
    {
        $table = [
            '15-1018' => '系統無法處理。銀行主機忙碌中或銀行線路中斷',
            '34-171'  => '金融交易失敗',
            '8-204'   => '訂單編號重複',
            '52-554'  => '使用者帳號密碼錯誤',
        ];
        if ( isset($table["$PRC-$SRC"]) ) {
            return $table["$PRC-$SRC"];
        } elseif ( isset($table["$PRC-*"]) ) {
            return $table["$PRC-*"];
        } elseif ( isset($table["*-$SRC"]) ) {
            return $table["*-$SRC"];
        }
        return $this->translatePRCMessage($PRC) . ': ' . $this->translateSRCMessage($SRC);
    }

    /**
     * @param string $PRC
     */
    public function translatePRCMessage($PRC) {
        $table = [
            '0' => '作業順利完成。',
            '2' => '找不到指定的物件。',
            '3' => '找不到必要參數。',
            '6' => '必要參數的格式不正確。',
            '7' => '必要參數的值不正確。',
            '8' => '有重複物件存在。',
            '10' => '剖析輸入串流時發生錯誤。',
            '11' => '對此動作而言,物件未處於正確狀態。',
            '12' => 'Payment Manager 中發生通信錯誤。',
            '13' => 'Payment Manager 遇到非預期的內部錯誤。',
            '14' => '發生資料庫通信錯誤。',
            '15' => '發生卡匣特定錯誤。相關說明請參閱卡匣補充資訊。',
            '32' => '不容許 API 指令中所指定的參數組合。',
            '34' => '因金融理由導致作業失敗。',
            '43' => '為特定商店做的風險控管',
            '52' => '進行使用者授權期間發生錯誤。',
            '55' => '指令名稱未被視為有效的 $til; 指令。',
        ];
        if ( isset($table[$PRC]) ) {
            return $table[$PRC];
        } else {
            return "未定義訊息 [$PRC] 請查詢藍新提供之 PRC 表格";
        }
    }


    /**
     * @param string $SRC
     */
    public function translateSRCMessage($SRC) {
        $table = [
            '0' => '無其它資訊可用。',
            '3' => '不明指令。',
            '4' => '發生異常錯誤。',
            '10' => '不支援之編碼。',
            '110' => '此回應與商家號碼參數有關。',
            '111' => '此回應與訂單號碼參數有關。',
            '112' => '此回應與 ORDERDATE 參數有關。',
            '113' => '此回應與 BATCHCLOSEDATE 參數有關。',
            '114' => '此回應與 BATCHNUMBER 參數有關。',
            '117' => '此回應與 AMOUNT 參數有關。',
            '118' => '此回應與 AMOUNTEXP10 參數有關。',
            '119' => '此回應與 CURRENCY 參數有關。',
            '130' => '此回應與訂單 URL 參數有關。',
            '171' => '查看卡匣特定資料取得進一步資訊。',
            '202' => '此回應與商家付款系統(如 SET)有關。',
            '204' => '此回應與訂單實體有關。',
            '205' => '此回應與付款實體有關。',
            '206' => '此回應與退款實體有關。',
            '207' => '此回應與批次實體有關。',
            '309' => '發生通信錯誤。',
            '512' => '連接資料庫或執行 SQL 陳述式時發生錯誤。',
            '554' => '指定的使用者無權執行所要求的作業。',
            '1015' => '此回應與 PAN 參數(指定於通信協定資料中)有關。',
            '1016' => '此回應與過期參數(指定於通信協定資料中)有關。',
            '1018' => '卡匣與其所通信之實體間發生通信錯誤。',
            '1200' => '此回應與請款指標參數有關。',
            '1201' => '此回應與訂單明細參數有關。',
            '1202' => '此回應與信用卡卡號參數有關。',
            '2005' => 'XIDINDEX',
            '2006' => 'CAVV',
            '2007' => 'ECI',
            '2009' => 'ERRORCODE',
            '2011' => 'PINCODE',
            '2015' => 'ID',
            '2016' => '此回應與分期付款期數參數有關。',
            '2017' => 'CVV2',
            '2018' => '此回應與授權碼參數有關。',
            '2050' => '此回應與訂單說明參數有關。',
            '2052' => 'REDEMPTION',
            '2053' => '此回應與起始日期參數有關。',
            '2054' => '此回應與結束日期參數有關。',
            '2055' => '此回應與郵遞區號參數有關。',
            '4001' => '限額阻擋,交易超過額度上限。',
            '4003' => '限額阻擋,單筆交易金額低於下限。',
            '4004' => '系統黑名單。',
            '4005' => '商店黑名單。',
            '4006' => '白名單。',
            '4007' => '僅接受國內卡。',
            '4008' => '僅接受國外卡。',
            '4009' => '僅接受自行卡。',
            '4010' => '請款天數限制。',
            '4011' => '退款天數限制。',
            '5015' => '銀行 Payment Gateway 商家代碼,非特店代號',
            '5013' => '此回應與商品代號參數有關。',
            '5014' => '此回應與交易序號參數有關。',
            '5020' => '此回應與商品總數參數有關。',
        ];
        if ( isset($table[$SRC]) ) {
            return $table[$SRC];
        } else {
            return "未定義訊息 [$SRC] 請查詢藍新提供之 PRC 表格";
        }
    }

    public function validateConfig() {
        $bundle = kernel()->bundle('CartBundle');

        // Move to config validation
        if ( ! $bundle->config('Transaction.Neweb.MerchantNumber') ) {
            throw new Exception('Transaction.Neweb.MerchantNumber is required.');
        }
        if ( ! $bundle->config('Transaction.Neweb.Code') ) {
            throw new Exception('Transaction.Neweb.Code is required.');
        }
    }

    public function getFormData() {
        $bundle = kernel()->bundle('CartBundle');
        $config = $bundle->config; // CartBundle config

        $order = $this->getCurrentOrder();
        if ( false === $order ) {
            return null;
        }

        $this->validateConfig();

        $merchantNumber = $bundle->config('Transaction.Neweb.MerchantNumber');
        $code = $bundle->config('Transaction.Neweb.Code');
        $rcode = $bundle->config('Transaction.Neweb.RCode');

        $checkstr =
              $merchantNumber
            . $order->sn
            . $rcode
            . $order->total_amount;
        $checksum = md5($checkstr);
        return array(
            'config' => $bundle->config('Transaction.Neweb'),
            'mobile' => $this->isMobile() ? 1 : 0,
            'english' => kernel()->locale->current() != 'zh_TW' ? 1 : 0,
            'order' => $order,
            'checksum' => $checksum,
        );
    }

    /**
     * Neweb payment form page
     */
    public function indexAction() {
        $bundle = kernel()->bundle('CartBundle');
        $config = $bundle->config;

        $order = $this->getCurrentOrder();
        if ( false === $order ) {
            die('parameter error');
            return $this->redirect('/');
        }
        $formData = $this->getFormData();
        return $this->render("order_payment_credit_card.html", [ 'neweb' => $formData ]);
    }

    public function getParameter($n) 
    {
        return isset($_POST[$n]) ? $_POST[$n] : "";
    }

    public function returnAction() {
        $bundle = kernel()->bundle('CartBundle');

        $finalResult             = $this->getParameter('final_result');
        $merchantNumber          = $this->getParameter('P_MerchantNumber');
        $orderNumber             = $this->getParameter('P_OrderNumber');
        $amount                  = $this->getParameter('P_Amount');
        $checkSum                = $this->getParameter('P_CheckSum');
        $finalReturn_PRC         = $this->getParameter('final_return_PRC');
        $finalReturn_SRC         = $this->getParameter('final_return_SRC');
        $finalReturn_ApproveCode = $this->getParameter('final_return_ApproveCode');
        $finalReturn_BankRC      = $this->getParameter('final_return_BankRC');
        $finalReturn_BatchNumber = $this->getParameter('final_return_BatchNumber');

        $code = $bundle->config('Transaction.Neweb.Code');

        $message = "交易失敗";
        $reason = '';
        $result = false;
        if ( $finalResult == "1" ) {
            if( strlen($checkSum)>0){
                $checkstr = md5($merchantNumber . $orderNumber . $finalResult . $finalReturn_PRC . $code. $finalReturn_SRC . $amount);
                if ( strtolower($checkstr) == strtolower($checkSum)){
                    $message = "交易成功";
                    $reason  = "感謝您的訂購，您的訂單已經成立。";
                    $result = true;
                } else {
                    $reason = "交易發生問題，驗證碼錯誤!";
                }
            }
        } else {
            $reason = $this->translateCustomerMessage($finalReturn_PRC, $finalReturn_SRC);
        }

        $desc = [
            '結果'       => $finalResult,
            '店家編號'   => $merchantNumber,
            '訂單編號'   => $orderNumber,
            '交易金額'   => $amount,
            '授權碼'     => $finalReturn_ApproveCode,
            '銀行回傳碼' => $finalReturn_BankRC,
            '批次號碼'   => $finalReturn_BatchNumber,
            '檢查碼'     => $checkSum,
            '主回傳碼'  => $finalReturn_PRC,
            '副回傳碼'  => $finalReturn_SRC,
            '回傳訊息'  => $this->translateMessage($finalReturn_PRC, $finalReturn_SRC),
        ];

        $order = new Order;
        $order->load([ 'sn' =>  $orderNumber ]);
        if ( ! $order->id ) {
            die('無此訂單');
        }
        $order->update(['payment_type' => 'cc']); // credit card

        try {
            // record the transction
            $txn = new Transaction;
            $ret = $txn->create([
                'order_id' => $order->id,
                'type'     => 'cc',
                'result'   => $result,
                'message'  => $message,
                'amount'   => intval($amount),
                'reason'   => $this->translateMessage($finalReturn_PRC, $finalReturn_SRC),
                'code'     => $finalReturn_BankRC,
                'data'     => yaml_emit($desc, YAML_UTF8_ENCODING),
                'raw_data' => yaml_emit($_POST, YAML_UTF8_ENCODING),
            ]);

            if ( ! $ret->success ) {
                throw new Exception($ret->message);
            }

        }  catch ( Exception $e ) {
            $order->regenerateSN();
            error_log($e->message);
        }

        // regenerateSN if the transaction is failed.
        if ( $result ) {
            $email = new PaymentCreditCardEmail($order->member, $order);
            $email->send();
        } else {
            $order->regenerateSN();
        }

        return $this->render('message.html', [
            'error' => ! $result,
            'title' => $message,
            'message'  => $reason,
        ]);
    }

    public function responseAction() {
        $bundle = kernel()->bundle('CartBundle');

        // Record the transaction
        $merchantNumber   = $this->getParameter('MerchantNumber');
        $orderNumber      = $this->getParameter('OrderNumber');
        $PRC              = $this->getParameter('PRC');
        $SRC              = $this->getParameter('SRC');
        $amount           = $this->getParameter('Amount');
        $checkSum         = $this->getParameter('CheckSum');
        $approvalCode     = $this->getParameter('ApprovalCode');
        $bankResponseCode = $this->getParameter('BankResponseCode');
        $batchNumber      = $this->getParameter('BatchNumber');
        $code = $bundle->config('Transaction.Neweb.Code');


        // api data with description
        $desc = [ 
            '訂單編號'   => $orderNumber,
            '交易金額'   => $amount,
            '授權碼'     => $approvalCode,
            '銀行回傳碼' => $bankResponseCode,
            '批次號碼'   => $batchNumber,
        ];

        // fail by default
        $result = false;
        $message = '交易失敗';
        $reason  = '';


        if ( $PRC =="0" && $SRC == "0" ) {
            $chkstr = $merchantNumber.$orderNumber.$PRC.$SRC.$code.$amount;
            $chkstr = md5($chkstr);

            $desc['檢查碼']     = $checkSum;
            $desc['驗證碼']     = $chkstr;

            // -- 回傳成功，但結果有可能遭竄改，因此需和編碼內容比較
            if (strtolower($chkstr)==strtolower($checkSum)) {
                $result = true;
                $message = '交易成功';
                // $desc['狀態'] = '交易成功';
            } else {
                $message = '交易失敗';
                //-- 資料遭竄改
                $reason  = '交易結果有誤，請與藍新聯絡!';
            }
        } else if ( $PRC == "15" && $SRC = "1018" ) {
            // XXX: 
            // PRC=15,SRC=1018 
            $reason = '系統無法處理 (request not to Issuer yet) the transaction normally due to the reason that bank hosts busy or networks break transiently.';
        } else if ( $PRC=="34" && $SRC=="171") {
            $reason = '金融失敗';
        } else if ( $PRC=="8" && $SRC=="204") {
            $reason = '訂單編號重複!';
        } else if ( $PRC=="52" && $SRC=="554") {
            $reason = '使用者帳號密碼錯誤!';
        } else {
            $reason = '系統錯誤';
        }

        $order = new Order;
        $order->load([ 'sn' =>  $orderNumber ]);
        if ( ! $order->id ) {
            die('無此訂單');
        }

        // record the transction
        $txn = new Transaction;
        $ret = $txn->create([
            'order_id' => $order->id,
            'result'   => $result,
            'type'     => 'cc',
            'amount'   => $amount,
            'message'  => $message,
            'reason'   => $reason,
            'code'     => $bankResponseCode,
            'data'     => yaml_emit($desc, YAML_UTF8_ENCODING),
            'raw_data' => yaml_emit($_POST, YAML_UTF8_ENCODING),
        ]);
        if ( ! $ret->success ) {
            // XXX: log the error
        }
    }
}

