<?php

namespace CartBundle\CartStorage;

use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
use ArrayIterator;
use IteratorAggregate;
use ArrayAccess;
use Countable;

/**
 * Session storage for cart.
 *
 * We only stores order item id for current user.
 */
class SessionCartStorage
    implements IteratorAggregate, ArrayAccess, Countable, CartStorage
{
    public function removeAll()
    {
        unset($_SESSION['items']);
        $_SESSION['items'] = array();
    }

    public function empty()
    {
        return empty($_SESSION['items']);
    }

    public function count()
    {
        return count($_SESSION['items']);
    }

    /**
     * set new items of array
     */
    public function set(array $items)
    {
        $_SESSION['items'] = $items;
    }

    public function add(OrderItem $item)
    {
        $_SESSION['items'][] = $item->id;
    }

    public function all() : OrderItemCollection
    {
        if (!isset($_SESSION['items'])) {
            return false;
        }
        $collection = new OrderItemCollection;
        foreach ($_SESSION['items'] as $id) {
            $item = new OrderItem;
            $ret = $item->find(intval($id));
            if ($ret->success) {
                $collection->add($item);
            }
        }
        return $collection;
    }

    public function contains(OrderItem $item)
    {
        if (isset($_SESSION['items']) && is_array($_SESSION['items'])) {
            return in_array($item->id, $_SESSION['items']);
        }

        return false;
    }

    public function remove(OrderItem $item)
    {
        $itemId = intval($item->id);
        $items = $this->get();
        if ($items && !empty($items)) {
            $idx = array_search($itemId, $this->get());
            if ($idx !== false) {
                array_splice($_SESSION['items'], $idx, 1);

                return true;
            }
        }
        return false;
    }

    public function getIterator()
    {
        return $this->all();
    }

    public function offsetSet($idx, $value)
    {
        $_SESSION[$idx] = $value;
    }

    public function offsetExists($idx)
    {
        return isset($_SESSION[ $idx ]);
    }

    public function offsetGet($idx)
    {
        return $_SESSION[ $idx ];
    }

    public function offsetUnset($idx)
    {
        unset($_SESSION[$idx]);
    }
}
