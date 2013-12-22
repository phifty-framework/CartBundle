<?php
namespace CartBundle\Action;
use ActionKit\Action;
use ActionKit\RecordAction\CreateRecordAction;
use CartBundle\Cart;

class Checkout extends CreateRecordAction
{
    public $recordClass = 'CartBundle\\Model\\Order';

    public function schema() {
        $this->useRecordSchema();

        $prefixes = [
            '購買人' => 'sender_',
            '收貨人' => 'shipping_'
        ];

        /*
         * XXX:
        foreach( $prefixes as $label => $prefix ) {
            $this->param("{$prefix}phone_extension")->isa('str')->label("$label 分機");
        }
        */

        // we don't trust amount fields from outside
        $this->filterOut('paid_amount','total_amount','shipping_cost');
    }

    public function run()
    {
        if ( $t = $this->arg('invoice_type') ) {
            if ( intval($t) == 3 ) {
                $this->requireArgs('utc','utc_title','utc_name','utc_address');
                if ( $this->result->hasInvalidMessages() ) {
                    return $this->error(_('您選擇了三聯式發票，請確認欄位填寫喔。'));
                }
                if ( strlen(trim($this->arg('utc'))) != 8 ) {
                    $this->invalidField('utc', _('統一編號必須是八碼，再麻煩您檢查一下') );
                }
            }
        }

        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();

        $shippingCost = $cart->calculateShippingCost();
        $totalAmount = $cart->calculateDiscountedTotalAmount();

        // Use Try-Cache to cache exceptions and process fallbacks.
        $this->setArgument('paid_amount', 0);
        $this->setArgument('total_amount', $totalAmount);
        $this->setArgument('shipping_cost', $shippingCost);

        // XXX: start transaction
        kernel()->db->beginTransaction();

        try {
            if ( ! parent::run() ) {
                throw new Exception( _('無法建立訂單') );
            }
            foreach( $orderItems as $orderItem ) {
                $ret = $orderItem->update([
                    'order_id' => $this->record->id,
                    'shipping_status' => 'unpaid',
                ]);
                if ( ! $ret->success ) {
                    if ( $ret->exception ) {
                        throw $ret->exception;
                    }
                    throw new Exception($ret->message);
                }
            }
            $cart->cleanUp();
            kernel()->db->commit();
            $this->success(_('訂單建立成功，導向中.. 請稍待'));
            return $this->redirectLater('/checkout/review?' . http_build_query([
                'o' => $this->record->id,
                't' => $this->record->token,
            ]), 3);
        } catch ( Exception $e ) {
            kernel()->db->rollback();
            return $this->error( $e->getMessage() );
        }
        return $this->error('訂單建立失敗');
    }

}
