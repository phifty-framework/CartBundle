<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\Cart;
use CartBundle\Model\Coupon;

class ApplyCoupon extends Action
{
    public function schema()
    {
        $this->param('coupon_code')
            ->required()
            ->label( _('折價券代碼') )
            ;
    }

    public function run() {
        $coupon = new Coupon(array('coupon_code' => $this->arg('coupon_code')));
        if (! $coupon->id) {
            return $this->error( _('無此折價券') );
        }
        $cart = Cart::getInstance();
        list($success, $reason) = $coupon->isValid($cart);
        if ( ! $success ) {
            return $this->error($reason);
        }
        if ($success) {
            $cart->applyCoupon($coupon);
            $summary = $cart->getSummary();
            $summary['discount'] = $coupon->discount;
            return $this->success(_('可用的折價券'), $summary);
        } else {
            $summary = $cart->getSummary();
            return $this->error(_('無法使用的折價券'), $summary);
        }
    }
}
