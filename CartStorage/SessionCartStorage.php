<?php

namespace CartBundle\CartStorage;

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

    public function isEmpty()
    {
        return empty($_SESSION['items']);
    }

    public function notEmpty()
    {
        return !empty($_SESSION['items']);
    }

    public function count()
    {
        return count($_SESSION['items']);
    }

    public function get()
    {
        if (isset($_SESSION['items'])) {
            return $_SESSION['items'];
        }

        return array();
    }

    public function set($items)
    {
        $_SESSION['items'] = $items;
    }

    public function add($itemId)
    {
        $_SESSION['items'][] = $itemId;
    }

    public function all()
    {
        return $_SESSION['items'];
    }

    public function contains($itemId)
    {
        if (isset($_SESSION['items']) && is_array($_SESSION['items'])) {
            return in_array($itemId, $_SESSION['items']);
        }

        return false;
    }

    public function remove($itemId)
    {
        $itemId = intval($itemId);
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
        return new ArrayIterator($this->getItems());
    }

    public function offsetSet($name, $value)
    {
        $_SESSION[ $name ] = $value;
    }

    public function offsetExists($name)
    {
        return isset($_SESSION[ $name ]);
    }

    public function offsetGet($name)
    {
        return $_SESSION[ $name ];
    }

    public function offsetUnset($name)
    {
        unset($_SESSION[$name]);
    }
}
