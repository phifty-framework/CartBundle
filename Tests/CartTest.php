<?php
namespace CartBundle\Tests;
use PHPUnit_Framework_TestCase;
use CartBundle\Cart;
use CartBundle\CartStorage\ArrayCartStorage;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderItem;
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

    public function testCartRemoveInvalidItems()
    {
        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $item = new OrderItem;
        $this->assertFalse($cart->validateItem($item));

        $cart->addItem($item);
        $invalidItems = $cart->removeInvalidItems();
        $this->assertCount(1, $invalidItems);
    }

    public function testCartAddItem()
    {
        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $product = new Product;
        $product->create([ 'name' => 'Clothes' ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => 10 ]);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');

        $item1 = $cart->addProduct($product, $type, 1);
        $cart->addItem($item1);
        $this->assertCount(1, $cart->storage->all(), 'Should be only one order item');
    }

    public function testContainsProduct()
    {
        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $product = new Product;
        $product->create([ 'name' => 'Clothes' ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => 10 ]);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');

        $item1 = $cart->addProduct($product, $type, 1);
        $cart->containsProduct($product);
    }

    public function testCartAddProduct()
    {
        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $product = new Product;
        $product->create([ 'name' => 'Clothes' ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => 10 ]);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');

        $item1 = $cart->addProduct($product, $type, 1);
        $this->assertEquals($product->id, $item1->product_id);
        $this->assertEquals($type->id, $item1->type_id, 'Type should be the same');
        $this->assertCount(1, $cart->storage->all(), 'Should be only one order item');

        $item2 = $cart->addProduct($product, $type, 1);
        $this->assertEquals($product->id, $item2->product_id);
        $this->assertEquals($type->id, $item2->type_id, 'Type should be the same');
        $this->assertCount(1, $cart->storage->all(), 'Should still one order item');
        $this->assertEquals($item1->id, $item2->id, 'Should be the same order item');
        $this->assertCount(1, $cart->storage->all());
    }

}




