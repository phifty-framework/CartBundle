<?php
namespace CartBundle\ShippingFeeRule;

interface ShippingFeeRule
{
    public function calculate(Cart $cart);
}

