<?php
namespace CartBundle\Tests;
use LazyRecord\Testing\ModelTestCase;
use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\Product;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\ProductType;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderSchema;
use CartBundle\Model\Order;


/**
 * Create an order base test case
 */
class OrderItemTest extends ModelTestCase
{
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

        $item = new OrderItem;
        $ret = $item->create([ 
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $this->assertResultSuccess($ret);
    }



}




