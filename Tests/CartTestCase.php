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
use CartBundle\Model\IncrementEntitySchema;
use CartBundle\ShippingFeeRule\NoShippingFeeRule;
use MemberBundle\Model\MemberSchema;
use ProductBundle\Model\ProductSchema;
use ProductBundle\Model\ProductTypeSchema;
use ProductBundle\Model\Product;
use LazyRecord\Testing\ModelTestCase;

abstract class CartTestCase extends ModelTestCase
{
    public $driver = 'testing';

    public function getModels()
    {
        return [
            new ProductTypeSchema,
            new ProductSchema,
            new OrderItemSchema,
            new OrderSchema,
            new CouponSchema,
            new LogisticsSchema,
            new MemberSchema,
            new IncrementEntitySchema,
        ];
    }
}
