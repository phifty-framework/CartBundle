<?php

namespace CartBundle\CartStorage;

use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;

/**
 * CartStorage stores only order item id list.
 */
interface CartStorage
{
    public function empty();

    public function count();

    /**
     * Return all order items
     *
     * @return OrderItemCollection
     */
    public function all();

    /**
     * @param OrderItem[]
     */
    public function set(array $items);

    /**
     * Add an order item to the storage
     */
    public function add(OrderItem $item);

    /**
     * Remove an order item from the storage
     */
    public function remove(OrderItem $item);

    /**
     * Check if the order item is already in the storage.
     */
    public function contains(OrderItem $item);
}
