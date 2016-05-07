<?php
namespace CartBundle\CartStorage;

use CartBundle\Model\OrderItem;
use ArrayIterator;
use IteratorAggregate;
use ArrayAccess;
use Countable;

class ArrayCartStorage 
    implements
        IteratorAggregate,
        ArrayAccess,
        Countable,
        CartStorage
{

    protected $items;

    public function __construct(array $initArray = array())
    {
        $this->items = $initArray;
    }

    public function removeAll()
    {
        $this->items = array();
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function notEmpty()
    {
        return !$this->isEmpty();
    }

    public function count()
    {
        return count($this->items);
    }

    public function get()
    {
        return $this->items;
    }

    public function set(array $items)
    {
        $this->items = $items;
    }

    public function add(OrderItem $item)
    {
        $this->items[] = $item->id;
    }

    public function all()
    {
        return $this->items;
    }

    public function contains(OrderItem $b)
    {
        foreach ($this->items as $a) {
            if (intval($a->id) == intval($b->id)) {
                return true;
            }
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
                array_splice($this->items, $idx, 1);
                return true;
            }
        }
        return false;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function offsetSet($name, $value)
    {
        $this->items[ $name ] = $value;
    }

    public function offsetExists($index)
    {
        return isset($this->items[$index]);
    }

    public function offsetGet($index)
    {
        return $this->items[$index];
    }

    public function offsetUnset($index)
    {
        unset($this->items[$index]);
    }

}
