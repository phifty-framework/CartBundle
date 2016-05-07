<?php

namespace CartBundle\CartStorage;

use CartBundle\Model\OrderItem;

/**
 * CartStorage stores only order item id list.
 */
interface CartStorage
{
    public function isEmpty();

    public function count();

    public function get();

    /**
     * @param OrderItem[]
     */
    public function set(array $items);

    /**
     *
     */
    public function add(OrderItem $item);

    public function remove(OrderItem $item);
}
