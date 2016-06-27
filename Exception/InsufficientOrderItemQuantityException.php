<?php
namespace CartBundle\Exception;
use CartBundle\Model\OrderItem;

class InsufficientOrderItemQuantityException extends CheckoutException
{
    protected $orderItem;

    protected $availableQuantity;

    public function __construct(OrderItem $orderItem, $message = null, $availableQuantity = null)
    {
        $this->orderItem = $orderItem;
        parent::__construct($message);
        $this->availableQuantity = $availableQuantity;
    }
}
