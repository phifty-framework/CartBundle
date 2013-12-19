<?php
namespace CartBundle;

interface CartStorage {

    public function isEmpty();

    public function count();

    public function get();

    public function set($items);

    public function add($itemId);

    public function remove($itemId);
}


