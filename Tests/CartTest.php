<?php
namespace CartBundle\Tests;
use PHPUnit_Framework_TestCase;
use CartBundle\Cart;
use CartBundle\CartStorage\ArrayCartStorage;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderItem;
use CartBundle\Model\Coupon;
use CartBundle\Model\CouponSchema;
use CartBundle\Model\OrderSchema;
use CartBundle\Model\LogisticsSchema;
use CartBundle\ShippingFeeRule\NoShippingFeeRule;
use MemberBundle\Model\Member;
use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\Product;
use LazyRecord\Testing\ModelTestCase;

class CartTest extends CartTestCase
{
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

    public function testMergeItems()
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

        $item2 = new OrderItem;
        $item2->create([
            'product_id' => $product->id,
            'type_id' => $type->id,
            'quantity' => 1,
        ]);
        $cart->addItem($item2);
        $this->assertCount(2, $cart->storage->all(), 'Should be only 2 order items');
        $cart->mergeItems();
        $this->assertCount(1, $cart->storage->all(), 'Should be only one order item after merge');
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

    public function testSimpleCoupon()
    {
        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $product = new Product;
        $product->create([ 'name' => 'Clothes', 'price' => 1000 ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => 10 ]);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');
        $cart->addProduct($product, $type, 1); // just one

        $coupon = new Coupon();
        $coupon->create([
            'discount' => 20,
            'required_amount' => 500,
        ]);
        $cart->setShippingFeeRule(new NoShippingFeeRule);
        $this->assertTrue($cart->applyCoupon($coupon));
        $this->assertEquals(980, $cart->calculateDiscountedTotalAmount());
        $this->assertEquals(0, $cart->calculateShippingFee());
    }

}




