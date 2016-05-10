<?php
namespace CartBundle\Tests;
use PHPUnit_Framework_TestCase;
use CartBundle\Cart;
use CartBundle\CartStorage\ArrayCartStorage;
use CartBundle\Model\OrderItemSchema;
use CartBundle\Model\OrderItem;
use CartBundle\Model\OrderSchema;
use CartBundle\Process\CheckoutProcess;
use CartBundle\ShippingFeeRule\NoShippingFeeRule;
use CartBundle\ShippingFeeRule\DefaultShippingFeeRule;

use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\Product;

use MemberBundle\Model\Member;

class CheckoutTest extends CartTestCase
{

    protected function getUserInfo()
    {
        return [
            'name'      => 'Yo-An Lin',
            'cellphone' => '0975277696',
            'address'   => '0975277696',
            'gender'    => 'male',
            'email'     => 'yoanlin93@gmail.com',
            'password'  => '12341234',
        ];
    }


    public function quantityProvider() {
        return [
            [10, 1, true],
            [10, 10, true],
            [10, 11, false],
        ];

    }


    /**
     * @dataProvider quantityProvider
     */
    public function testCartCheckoutWithProductTypeQuantity($totalQuantity, $itemQuantity, $shouldSuccess)
    {
        $userInfo = $this->getUserInfo();

        $member = new Member;
        $ret = $member->create($userInfo);
        $this->assertResultSuccess($ret);

        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $product = new Product;
        $product->create([ 'name' => 'Clothes' ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => $totalQuantity ]);

        $this->assertEquals($totalQuantity, $type->quantity);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');
        $cart->addProduct($product, $type, $itemQuantity);
        $cart->setShippingFeeRule(new NoShippingFeeRule);

        $args = [];
        foreach ($userInfo as $key => $value) {
            $args[ "buyer_$key" ] = $value;
            $args[ "shipping_$key" ] = $value;
        }
        $process = new CheckoutProcess($member, $cart);
        $process->setProductTypeQuantityEnabled(true); // this should update the product type quantity
        $process->checkout($args);

        $ret = $type->reload();
        $this->assertResultSuccess($ret);
        if ($shouldSuccess) {
            $this->assertEquals($totalQuantity - $itemQuantity, $type->quantity);
        } else {
            $this->assertEquals($totalQuantity, $type->quantity);
        }
    }

    /**
     *
     */
    public function testCartCheckout()
    {
        $userInfo = $this->getUserInfo();
        $member = new Member;
        $ret = $member->create($userInfo);
        $this->assertResultSuccess($ret);

        $cart = new Cart(new ArrayCartStorage);
        $this->assertEmpty($cart->storage->all());

        $product = new Product;
        $product->create([ 'name' => 'Clothes' ]);
        $type = $product->types->create([ 'name' => 'M', 'quantity' => 10 ]);

        $this->assertNotNull($type->id, 'product type exists');
        $this->assertNotNull($product->id, 'product exists');
        $this->assertEquals($product->id, $type->product_id, 'product type exists');
        $cart->addProduct($product, $type, 1);

        $cart->setShippingFeeRule(new NoShippingFeeRule);

        $args = [];
        foreach ($userInfo as $key => $value) {
            $args[ "buyer_$key" ] = $value;
            $args[ "shipping_$key" ] = $value;
        }
        $process = new CheckoutProcess($member, $cart);
        $process->checkout($args);
    }

}
