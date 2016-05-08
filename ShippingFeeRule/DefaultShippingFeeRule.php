<?php
namespace CartBundle\ShippingFeeRule;
use Phifty\Bundle;
use CartBundle\Cart;
use CartBundle\Model\Logistics;

/**
 * config based shipping fee rule
 */
class DefaultShippingFeeRule implements ShippingFeeRule
{
    protected $bundle;

    public function __construct(Bundle $bundle)
    {
        $this->bundle = $bundle;
    }

    public function calculate(Cart $cart)
    {
        if ($aboveAmount = $this->bundle->config('DefaultShippingFeeRule.AboveAmount')) {
            $orderItemAmount = $cart->calculateOrderItemTotalAmount();
            if ($orderItemAmount >= $aboveAmount) {
                return 0;
            }
        }
        return $this->bundle->config('DefaultShippingFeeRule.DefaultFee') ?: 80;
    }
}



