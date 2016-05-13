<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\CartBundle;

class CalculateShippingFee extends Action
{
    public function schema()
    {
        // Amount from items' subtotal
        $this->param('items_amount')
            ->isa('int')
            ->required()
            ;
    }

    public function run()
    {
        $bundle = CartBundle::getInstance();
        if ($shippingFeeRule = $bundle->getShippingFeeRule()) {
            $itemsAmount = intval($this->arg('items_amount'));
            return $this->success('success', ['shipping_fee' => $shippingFeeRule->byTotalAmount($itemsAmount) ]);
        }
        return $this->error('shipping fee rule is not defined in system config.');
    }
}
