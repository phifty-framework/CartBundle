<?php
namespace CartBundle;
use ArrayIterator;
use IteratorAggregate;
use ArrayAccess;
use Countable;
use CartBundle\CartStorage;

/**
 * Session storage for cart.
 *
 * We only stores order item id for current user.
 */
class SessionCartStorage
    implements IteratorAggregate, ArrayAccess, Countable, CartStorage
{

    public function isEmpty() {
        return empty($_SESSION['items']);
    }

    public function count() {
        return count($_SESSION['items']);
    }

    public function get() {
        if ( isset( $_SESSION['items'] ) ) {
            return $_SESSION['items'];
        }
        return array();
    }

    public function set($items) {
        $_SESSION['items'] = $items;
    }

    public function add($itemId) {
        $_SESSION['items'][] = $itemId;
    }

    public function remove($itemId) {
        $itemId = intval($itemId);
        $idx = array_search( $itemId, $_SESSION['items'] );
        if ( $idx !== false ) {
            array_splice( $_SESSION['items'], $idx , 1 );
        }
    }



    public function getIterator() {
        return new ArrayIterator( $this->getItems() );
    }

    public function offsetSet($name,$value)
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



