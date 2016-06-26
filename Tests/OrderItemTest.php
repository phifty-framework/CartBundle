<?php
namespace CartBundle\Tests;
use LazyRecord\Testing\ModelTestCase;
use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderItemCollection;
use CartBundle\Model\OrderSchema;
use CartBundle\Model\Order;


/**
 * Create an order base test case
 */
class OrderItemTest extends ModelTestCase
{
    public $driver = 'testing';


    public function getModels()
    {
        return [
            new ProductSchema,
            new ProductTypeSchema,
            new OrderItemSchema,
            new OrderSchema,
        ];
    }

    public function testQuantityDeduct()
    {
        $product = new Product;
        $ret = $product->create([
            'name' => 'Product A',
            'price' => 100,
        ]);
        $this->assertResultSuccess($ret);

        $product2 = new Product;
        $ret = $product2->create([
            'name' => 'Product B',
            'price' => 120,
        ]);
        $this->assertResultSuccess($ret);


        $item = new OrderItem;
        $ret = $item->create([ 
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
        $this->assertResultSuccess($ret);
        $this->assertEquals(1000, $item->calculateSubtotal());

        $item2 = new OrderItem;
        $ret = $item2->create([ 
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);
        $this->assertResultSuccess($ret);
        $this->assertEquals(360, $item2->calculateSubtotal());


        $items = new OrderItemCollection;
        $items->add($item);
        $items->add($item2);
        $this->assertEquals(1360, $items->calculateTotalAmount());
        $this->assertEquals(13, $items->calculateTotalQuantity());
    }
}

