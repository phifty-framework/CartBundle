<?php
namespace CartBundle\Model;
use CartBundle\Model\CouponBase;
class Coupon
    extends CouponBase
{

    /**
     * Check if the coupon code is valid.
     *
     *
     * TODO: we need to return the reason when validation fails.
     *
     * @return [boolean $success, string $reason]
     */
    public function isValid($cart) {
        if (!  $this->id ) {
            return [false, _("無效的折價券") ];
        }
        // use_limit: 0 == unlimited.
        if ( $this->use_limit > 0 && $this->used >= $this->use_limit ) {
            return [false, _("無效的折價券")];
        }
        if (  $this->required_amount > 0  && $cart->calculateOrderItemTotalAmount() < $this->required_amount ) {
            return [false, __("總金額需滿 %1 才可使用喔", $this->required_amount)];
        }
        return [true, _('可以使用的折價券') ];
    }

    public function increaseUsed() {
        $this->update([ 'used' => ['used + 1'] ]);
    }

    /**
     * Implement the logic for different coupon type in this method
     */
    public function calcualteDiscount($totalAmount) {
        $totalAmount = $totalAmount - $this->discount;
        if ( $totalAmount < 0 ) {
            return 0;
        }
        return $totalAmount;
    }
    
}
