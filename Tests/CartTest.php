<?php
namespace CartBundle\Tests;
use PHPUnit_Framework_TestCase;
use CartBundle\Cart;
use CartBundle\CartStorage\ArrayCartStorage;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderSchema;
use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\Product;
use LazyRecord\Testing\ModelTestCase;
// ModelTestCase
//

class CartTest extends ModelTestCase
{
    public $driver = 'testing';

    public function getModels()
    {
        return array(
            new ProductTypeSchema,
            new ProductSchema,
            new OrderItemSchema,
            new OrderSchema);
    }

    public function testCart()
    {
        $cart = new Cart(new ArrayCartStorage);
        $this->assertFalse($cart->fetchOrderItems());

        $product = new Product;
        $product->create([ 'name' => 'Cloth' ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => 10 ]);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');

        $item1 = $cart->addProduct($product, $type, 1);
        $this->assertEquals($product->id, $item1->product_id);
        $this->assertEquals($type->id, $item1->type_id, 'Type should be the same');
        $this->assertCount(1, $cart->fetchOrderItems(), 'Should be only one order item');

        $item2 = $cart->addProduct($product, $type, 1);
        $this->assertEquals($product->id, $item2->product_id);
        $this->assertEquals($type->id, $item2->type_id, 'Type should be the same');
        $this->assertCount(1, $cart->fetchOrderItems(), 'Should still one order item');

        $this->assertEquals($item1->id, $item2->id, 'Should be the same order item');

        $orderItems = $cart->fetchOrderItems();
        // $this->assertCount(1, $orderItems);
        // var_dump($orderItems->toArray());
    }

}




