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
        $P_MerchantNumber        = $this->getParameter('P_MerchantNumber');
        $P_OrderNumber           = $this->getParameter('P_OrderNumber');
        $P_Amount                = $this->getParameter('P_Amount');
        $P_CheckSum              = $this->getParameter('P_CheckSum');
        $finalReturn_PRC         = $this->getParameter('final_return_PRC');
        $finalReturn_SRC         = $this->getParameter('final_return_SRC');
        $finalReturn_ApproveCode = $this->getParameter('final_return_ApproveCode');
        $finalReturn_BankRC      = $this->getParameter('final_return_BankRC');
        $finalReturn_BatchNumber = $this->getParameter('final_return_BatchNumber');

        $Code = $bundle->config('Transaction.Neweb.Code');

        $message = "交易失敗";
        $reason = '';
        $error = true;
        if ( $finalResult == "1" ) {
            if( strlen($P_CheckSum)>0){
                $checkstr = md5($P_MerchantNumber . $P_OrderNumber . $finalResult . $finalReturn_PRC . $Code. $finalReturn_SRC . $P_Amount);
                if ( strtolower($checkstr) == strtolower($P_CheckSum)){
                    $message = "交易成功!";
                    $error = false;
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
        return $this->render('message.html', [
            'error' => $error,
            'message' => $message,
            'reason'  => $reason,
            'post'  => $_POST,
        ]);
    }

    public function responseAction() {
        // record the transction
        $txn = new Transaction;
        $txn->create([
            // 'order_id' => $_POST[''],
            'data' => $_POST,
        ]);
    }
}

