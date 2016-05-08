<?php
namespace CartBundle\Tests;
use PHPUnit_Framework_TestCase;
use CartBundle\Cart;
use CartBundle\CartStorage\ArrayCartStorage;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderSchema;
use CartBundle\Process\CheckoutProcess;
use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\Product;
use LazyRecord\Testing\ModelTestCase;

class CheckoutTest extends ModelTestCase
{
    public $driver = 'testing';

    public function getModels()
    {
        return [
            new ProductTypeSchema,
            new ProductSchema,
            new OrderItemSchema,
            new OrderSchema
        ];
    }

    public function testCartCheckout()
    {





    }

}
