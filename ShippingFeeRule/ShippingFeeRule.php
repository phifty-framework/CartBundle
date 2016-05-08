<?php

namespace CartBundle\ShippingFeeRule;

use CartBundle\Cart;

interface ShippingFeeRule
{
    public function calculate(Cart $cart);
}

