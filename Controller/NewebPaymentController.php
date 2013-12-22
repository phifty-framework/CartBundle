<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Model\Order;
use CartBundle\Model\Transaction;
use Exception;

class NewebPaymentController extends Controller
{
    public function indexAction() {
        $bundle = kernel()->bundle('CartBundle');
        $config = $bundle->config;

        $orderId = intval($this->request->param('o'));
        $token   = $this->request->param('t');
        if ( ! $orderId ) {
            die('parameter error');
            return $this->redirect('/');
        }

        $order = new Order;
        $ret = $order->load([
            'id' => $orderId,
            'token' => $token,
            ]);
        if ( ! $ret->success ||  ! $order->id ) {
            // XXX: show correct erro message
            die('order not found');
            return $this->redirect('/');
        }

        if ( $order->created_on ) {
            $orderPrefix = sprintf('%s%04s', $order->created_on->format('Ymd') , $order->id );
        } else {
            $orderPrefix = sprintf('%s%04s', date('Ymd') , $order->id );
        }

        if ( ! isset($config['Transaction']['Neweb']['MerchantNumber']) ) {
            throw new Exception('Transaction.Neweb.MerchantNumber is required.');
        }
        if ( ! isset($config['Transaction']['Neweb']['Code']) ) {
            throw new Exception('Transaction.Neweb.Code is required.');
        }

        $checkstr =
              $config['Transaction']['Neweb']['MerchantNumber']
            . $orderPrefix
            . $config['Transaction']['Neweb']['RCode']
            . $order->total_amount;
        $checksum = md5($checkstr);

        return $this->render("checkout_payment_credit_card.html", [
            'config' => $config,
            'isMobile' => $this->isMobile() ? 1 : 0,
            'english' => kernel()->locale->current() != 'zh_TW' ? 1 : 0,
            'order' => $order,
            'orderPrefix' => $orderPrefix,
            'checksum' => $checksum,
        ]);
    }

    public function getParameter($pname){
        return isset($_POST[$pname])?$_POST[$pname]:"";
    }

    public function returnAction() {
        $bundle = kernel()->bundle('CartBundle');

        $finalResult             = $this->getParameter('final_result');
        $merchantNumber        = $this->getParameter('P_MerchantNumber');
        $orderNumber           = $this->getParameter('P_OrderNumber');
        $amount                = intval($this->getParameter('P_Amount'));
        $checkSum              = $this->getParameter('P_CheckSum');
        $finalReturn_PRC         = $this->getParameter('final_return_PRC');
        $finalReturn_SRC         = $this->getParameter('final_return_SRC');
        $finalReturn_ApproveCode = $this->getParameter('final_return_ApproveCode');
        $finalReturn_BankRC      = $this->getParameter('final_return_BankRC');
        $finalReturn_BatchNumber = $this->getParameter('final_return_BatchNumber');

        $Code = $bundle->config('Transaction.Neweb.Code');

        $message = "交易失敗";
        $reason = '';
        $result = false;
        if ( $finalResult == "1" ) {
            if( strlen($checkSum)>0){
                $checkstr = md5($merchantNumber . $orderNumber . $finalResult . $finalReturn_PRC . $Code. $finalReturn_SRC . $amount);
                if ( strtolower($checkstr) == strtolower($checkSum)){
                    $message = "交易成功";
                    $result = true;
                } else {
                    $reason = "交易發生問題，驗證碼錯誤!";
                }
            }
        } else {
            if ( $finalReturn_PRC == "8" && $finalReturn_SRC == "204"){
                $reason = "訂單編號重複";
            } else if ( $finalReturn_PRC == "34" && $finalReturn_SRC == "171" ) {
                $reason = "銀行交易失敗。 銀行回傳碼 [" . $finalReturn_BankRC . "]";
            } else {
                $reason = "請與商家聯絡";
            }
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
        ];

        // record the transction
        $txn = new Transaction;
        $ret = $txn->create([
            'order_id' => intval($orderNumber),
            'result'   => $result,
            'message'  => $message,
            'reason'   => $reason,
            'code'     => $finalReturn_BankRC,
            'data'     => yaml_emit($desc),
            'raw_data' => yaml_emit($_POST),
        ]);
        if ( $ret->success ) {
            // set the total
        } else {
            // XXX: log the error
        }

        $order = new Order;
        $order->load( intval($orderNumber));
        if ( $order->id ) {
            if ( $result ) {
                $order->update([ 'paid_amount' => $amount ]);
                if ( $amount >= $order->total_amount ) {
                    $order->update([ 'payment_status' => 'paid' ]);
                } else {
                    $order->update([ 'payment_status' => 'paid' ]);
                }
            } else {
                $order->update([ 'payment_status' => 'paid_error' ]);
            }
        } else {
            die('無此訂單');
        }
        return $this->render('message.html', [
            'error' => $error,
            'message' => $message,
            'reason'  => $reason,
        ]);
    }

    public function responseAction() {
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

        // fail by default
        $result = false;
        $message = '交易失敗';
        $reason  = '';


        // api data with description
        $desc = [ 
            '訂單編號'   => $orderNumber,
            '交易金額'   => $amount,
            '授權碼'     => $approvalCode,
            '銀行回傳碼' => $bankResponseCode,
            '批次號碼'   => $batchNumber,
        ];

        if ( $PRC =="0" && $SRC == "0" ) {
            $chkstr = $merchantNumber.$orderNumber.$PRC.$SRC.$code.$amount;
            $chkstr = md5($chkstr);

            $desc['檢查碼']     = $checkSum;
            $desc['驗證碼']     = $chkstr;


            // -- 回傳成功，但結果有可能遭竄改，因此需和編碼內容比較
            if (strtolower($chkstr)==strtolower($CheckSum)) {
                $result = true;
                $message = '交易成功';
                // $desc['狀態'] = '交易成功';
            } else {
                $message = '交易失敗';
                //-- 資料遭竄改
                $reason  = '交易結果有誤，請與藍新聯絡!';
            }
        } else if ( $PRC=="34" && $SRC=="171") {
            $reason = '金融失敗';
        } else if ( $PRC=="8" && $SRC=="204") {
            $reason = '訂單編號重複!';
        } else if ( $PRC=="52" && $SRC=="554") {
            $reason = '使用者帳號密碼錯誤!';
        } else {
            $reason = '系統錯誤';
        }

        // record the transction
        $txn = new Transaction;
        $ret = $txn->create([
            'order_id' => intval($orderNumber),
            'result'   => $result,
            'message'  => $message,
            'reason'   => $reason,
            'code'     => $bankResponseCode,
            'data'     => yaml_emit($desc),
            'raw_data' => yaml_emit($_POST),
        ]);
        if ( ! $ret->success ) {
            // XXX: log the error
        }
    }
}

