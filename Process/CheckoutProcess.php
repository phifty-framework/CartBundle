<?php
namespace CartBundle\Process;
use CartBundle\Cart;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;
use Exception;
use MemberBundle\Model\Member;
use CartBundle\Email\OrderCreatedEmail;
use CartBundle\CartBundle;

use LazyRecord\Result;

class CheckoutException extends Exception
{

}

class InvalidOrderFormException extends CheckoutException
{
    protected $result;

    public function __construct($message, Result $result, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->result = $result;
    }
}

class CheckoutProcess
{
    protected $cart;

    protected $member;

    public function __construct(Member $member, Cart $cart)
    {
        $this->member = $member;
        $this->cart = $cart;
    }


    public function preprocess()
    {
        $this->cart->removeInvalidItems(true, true);
    }
    /**
     * @param array $args argument array contains basic information.
     */
    public function checkout(array $args)
    {
        // preprocess with cart items
        $shippingFee = $this->cart->calculateShippingFee();
        $origTotalAmount = $this->cart->calculateTotalAmount();
        $totalAmount = $this->cart->calculateDiscountedTotalAmount();
        $discountAmount = $this->cart->calculateDiscountAmount();

        // Use Try-Cache to cache exceptions and process fallbacks.
        $args['paid_amount'] = 0;
        $args['shipping_fee'] = $shippingFee;
        $args['total_amount'] =  $totalAmount;
        $args['discount_amount'] = $discountAmount;
        $args['member_id'] = $this->member->id;

        if ($coupon = $this->cart->getCurrentCoupon()) {
            $args['coupon_code'] = $coupon->coupon_code;
        }

        $order = new Order;
        $ret = $order->create($args);
        if (!$ret || $ret->error || !$order->id) {
            throw new InvalidOrderFormException(_('無法建立訂單'), $ret);
        }

        /*
        if ($coupon) {
            $coupon->update(['used' => ['used + 1']]);
        }
        */
        $bundle = CartBundle::getInstance();
        foreach ($this->cart as $orderItem) {
            $orderItem->setAlias('oi');
            $ret = $orderItem->update([
                'order_id'        => $order->id,
                'delivery_status' => 'unpaid',
            ]);
            if ($ret->error) {
                if ($ret->exception) {
                    throw $ret->exception;
                }
                throw new CheckoutException("無法更新訂單項目: {$ret->message}");
            }
            if ($bundle && $bundle->config('UseProductTypeQuantity')) {
                kernel()->db->query('LOCK TABLES '.ProductType::table.' AS t WRITE');
                $stmt = kernel()->db->prepare('UPDATE '.ProductType::table.' t SET quantity = quantity - ? WHERE id = ?');
                $stmt->execute([$orderItem->quantity, $orderItem->type_id]);
                kernel()->db->query('UNLOCK TABLES');
            }
        }
        $this->postProcess($order);
        return true;
    }

    public function postProcess(Order $order)
    {
        $email = new OrderCreatedEmail($this->member, $order);
        $email->send();
    }

    public function finalize()
    {
        $this->cart->cleanUp();
    }
}

