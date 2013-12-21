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
            '寄件人' => 'sender_',
            '收貨人' => 'shipping_'
        ];

        /*
        foreach( $prefixes as $label => $prefix ) {
            $this->param("{$prefix}phone_extension")->isa('str')->label("$label 分機");
        }
        */

        // we don't trust amount from outside
        $this->filterOut('paid_amount','total_amount');
    }

    public function run() {
        /*
        $senderPhone = $this->arg('sender_phone');
        if ( $senderPhoneExtension = $this->arg('sender_phone_extension') ) {
            $senderPhone .= '#' . $senderPhoneExtension;
        }
        $this->setArgument('sender_phone', $senderPhone);
         */
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        $totalAmount = $cart->calculateDiscountedTotalAmount();

        // Use Try-Cache to cache exceptions and process fallbacks.
        $this->setArgument('total_amount', $totalAmount);
        if ( $ret = parent::run() ) {
            foreach( $orderItems as $orderItem ) {
                $ret = $orderItem->update([ 'order_id' => $this->record->id ]);
                if ( ! $ret->success ) {
                }
            }
            $this->success(_('訂單建立成功'));
            return $this->redirectLater('/checkout/review', 3);
        } else {
            return $this->error( _('訂單建立失敗') );
        }
    }
}
