<?php
namespace CartBundle\Action;
use ActionKit\Action;
use CartBundle\CartBundle;

class CalculateShippingFee extends Action
{
    public function schema()
    {
        $this->param('items_cnt');

        // Amount from items' subtotal
        $this->param('items_amount')
            ->isa('int')
            ->required()
            ;
    }

    public function run()
    {
        $bundle = CartBundle::getInstance();

        $cnt = $this->arg('items_cnt');
        if ($cnt !== null) {
            if (intval($cnt) === 0) {
                // zero items should return 0 shipping fee
                return $this->success('success', ['shipping_fee' => 0 ]);
            }
        }


        if ($shippingFeeRule = $bundle->getShippingFeeRule()) {
            $itemsAmount = intval($this->arg('items_amount'));
            return $this->success('success', ['shipping_fee' => $shippingFeeRule->byTotalAmount($itemsAmount) ]);
        }
        return $this->error('shipping fee rule is not defined in system config.');
    }
}
