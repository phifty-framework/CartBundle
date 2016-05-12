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

    /**
     * Calculate the shipping fee by total amount
     */
    public function byTotalAmount($orderItemAmount)
    {
        if ($aboveAmount = $this->bundle->config('DefaultShippingFeeRule.AboveAmount')) {
            if ($orderItemAmount >= $aboveAmount) {
                return 0;
            }
        }
        return $this->bundle->config('DefaultShippingFeeRule.DefaultFee') ?: 80;
    }

    public function calculate(Cart $cart)
    {
        return $this->byTotalAmount($cart->calculateOrderItemTotalAmount());
    }
}



