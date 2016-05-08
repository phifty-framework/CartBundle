<?php

namespace CartBundle\ShippingFeeRule;

use Phifty\Bundle;
use CartBundle\Cart;
use CartBundle\Model\Logistics;

/**
 * NoShippingFeeRule will always return 0
 */
class NoShippingFeeRule implements ShippingFeeRule
{
    public function calculate(Cart $cart)
    {
        return 0;
    }
}



