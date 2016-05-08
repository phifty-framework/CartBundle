<?php
namespace CartBundle\ShippingFeeRule;
use Phifty\Bundle;
use CartBundle\Cart;
use CartBundle\Model\Logistics;

class DefaultShippingFeeRule implements ShippingFeeRule
{
    protected $bundle;

    protected $defaultShippingFee;

    public function __construct(Bundle $bundle, $defaultShippingFee = 80)
    {
        $this->bundle = $bundle;
        $this->defaultShippingFee = $defaultShippingFee;
    }

    public function calculate(Cart $cart)
    {
        if ($aboveAmount = $this->bundle->config('NoShippingFeeCondition.AboveAmount')) {
            $orderItemAmount = $cart->calculateOrderItemTotalAmount();
            if ($orderItemAmount >= $aboveAmount) {
                return 0;
            }
        }
        return $this->defaultShippingFee;
    }
}



