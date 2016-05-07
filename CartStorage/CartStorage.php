<?php

namespace CartBundle\CartStorage;

/**
 * CartStorage stores only order item id list.
 */
interface CartStorage
{
    public function isEmpty();

    public function count();

    public function get();

    public function set($items);

    public function add($itemId);

    public function remove($itemId);
}
