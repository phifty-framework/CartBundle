<?php

namespace CartBundle\Action;

use ActionKit\RecordAction\CreateRecordAction;
use MemberBundle\CurrentMember;
use ProductBundle\Model\ProductType;
use CartBundle\Cart;
use CartBundle\Model\OrderItem;
use CartBundle\ShippingFeeRule\DefaultShippingFeeRule;
use CartBundle\ShippingFeeRule\NoShippingFeeRule;
use CartBundle\Model\Order;
use CartBundle\Model\Coupon;
use CartBundle\Email\OrderCreatedEmail;
use Exception;

class Checkout extends CreateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\Order';

    public function schema()
    {
        $this->useRecordSchema();

        // we don't trust amount fields from outside
        $this->filterOut(
            'paid_amount',
            'total_amount',
            'discount_amount',
            'shipping_fee',
            'member_id',
            'payment_status',
            'payment_type'
        );
    }

    public function run()
    {
        $bundle = kernel()->bundle('CartBundle');

        $currentMember = new CurrentMember();
        if (!$currentMember->isLogged()) {
            return $this->error(_('請先登入會員'));
        }

        if ($t = $this->arg('invoice_type')) {
            if ($bundle->config('RequireUTCNameAndAddress') == 'always') {
                $this->requireArgs('utc_name', 'utc_address');
                if ($this->result->hasInvalidMessages()) {
                    return $this->error(_('請填寫發票收件人以及地址。'));
                }
            }

            if (intval($t) == 3) {
                $this->requireArgs('utc', 'utc_title', 'utc_name', 'utc_address');
                if ($this->result->hasInvalidMessages()) {
                    return $this->error(_('您選擇了三聯式發票，請確認欄位填寫喔。'));
                }
                if (strlen(trim($this->arg('utc'))) != 8) {
                    $this->invalidField('utc', _('統一編號必須是八碼，再麻煩您檢查一下'));

                    return $this->error(_('發票欄位填寫錯誤'));
                }
            }
        }

        if ($bundle->config('ChooseDeliveryType')) {
            if ($this->arg('delivery_type') == 'store') {
                $this->requireArgs('delivery_store');
                if ($this->result->hasInvalidMessages()) {
                    return $this->error(_('請選擇收貨店家'));
                }
            }
        }

        $cart = Cart::getInstance();
        if (count($cart) === 0) {
            return $this->error(_('購物車是空的'));
        }

        // set config based default shipping rule.
        $cart->setShippingFeeRule($bundle->getShippingFeeRule());

        $shippingFee = $cart->calculateShippingFee();
        $origTotalAmount = $cart->calculateTotalAmount();
        $totalAmount = $cart->calculateDiscountedTotalAmount();
        $discountAmount = $cart->calculateDiscountAmount();

        // Use Try-Cache to cache exceptions and process fallbacks.
        $this->setArgument('paid_amount', 0);
        $this->setArgument('shipping_fee', $shippingFee);
        $this->setArgument('total_amount', $totalAmount);
        $this->setArgument('discount_amount', $discountAmount);
        $this->setArgument('member_id', $currentMember->id);



        if (isset($_SESSION['coupon_code'])) {
            $coupon = new Coupon;
            $ret = $coupon->load(['coupon_code' => $_SESSION['coupon_code']]);
            if ($ret->success && $cart->applyCoupon($coupon)) {
                $this->setArgument('coupon_code', $coupon->coupon_code);
            } else {
                // if it's invalid coupon, just delete the sesssion
                unset($_SESSION['coupon_code']);
            }
        }

        kernel()->db->beginTransaction();

        try {
            $ret = parent::run();
            if (!$ret) {
                throw new Exception(_('無法建立訂單'));
            }

            if (!$this->record->id) {
                throw new Exception(_('無法建立訂單項目'));
            }

            if ($coupon) {
                $coupon->update(['used' => ['used + 1']]);
            }

            foreach ($orderItems as $orderItem) {
                $orderItem->setAlias('oi');

                $ret = $orderItem->update([
                    'order_id' => $this->record->id,
                    'delivery_status' => 'unpaid',
                ]);

                if ($ret->success) {
                    if ($bundle->config('UseProductTypeQuantity')) {
                        kernel()->db->query('LOCK TABLES '.ProductType::table.' AS t WRITE');
                        $stmt = kernel()->db->prepare('UPDATE '.ProductType::table.' t SET quantity = quantity - ? WHERE id = ?');
                        $stmt->execute([$orderItem->quantity, $orderItem->type_id]);
                        kernel()->db->query('UNLOCK TABLES');
                    }
                } else {
                    if ($ret->exception) {
                        throw $ret->exception;
                    }
                    throw new Exception('OrderItem update failed: '.$ret->message);
                }
            }

            kernel()->db->commit();

            $this->success(_('訂單建立成功，導向中.. 請稍待'));

            $email = new OrderCreatedEmail($currentMember->getRecord(), $this->getRecord());
            $email->send();

            $cart->cleanUp();

            // remove the current coupon
            unset($_SESSION['coupon_code']);

            return $this->redirectLater('/order/view?'.http_build_query([
                'o' => $this->record->id,
                't' => $this->record->token,
            ]), 2);
        } catch (Exception $e) {
            kernel()->db->rollback();

            return $this->error('訂單建立失敗:'.$e->getMessage());
        }

        return $this->error('訂單建立失敗');
    }
}
