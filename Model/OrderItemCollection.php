<?php
namespace CartBundle\Model;

class OrderItemCollection  extends \CartBundle\Model\OrderItemCollectionBase {

    /**
     * Calculate order item level total amount (excluding coupon)
     *
     * @return integer amount
     */
    public function calculateTotalAmount() {
        $amount = 0;
        foreach( $this as $item ) {
            $amount += $item->calculateAmount();
        }
        return $amount;
    }

    public function calculateTotalQuantity() {
        $quantity = 0;
        foreach( $this as $item ) {
            $quantity += $item->quantity;
        }
        return $quantity;
    }

}
