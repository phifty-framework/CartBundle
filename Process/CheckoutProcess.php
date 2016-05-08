<?php
namespace CartBundle\Process;
use CartBundle\Cart;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;
use Exception;

class CheckoutProcess
{
    protected $cart;

    protected $member;

    public function __construct(Member $member, Cart $cart)
    {
        $this->cart = $cart;
    }


    /**
     * @param array $args argument array contains basic information.
     */
    public function checkout(array $args)
    {
        // preprocess with cart items
        $shippingCost = $this->cart->calculateShippingCost();
        $origTotalAmount = $this->cart->calculateTotalAmount();
        $totalAmount = $this->cart->calculateDiscountedTotalAmount();
        $discountAmount = $this->cart->calculateDiscountAmount();

        // Use Try-Cache to cache exceptions and process fallbacks.
        $args['paid_amount'] = 0;
        $args['shipping_cost'] = $shippingCost;
        $args['total_amount'] =  $totalAmount;
        $args['discount_amount'] = $discountAmount;
        $args['member_id'] = $this->member->id;

        $coupon = $this->cart->loadSessionCoupon();
        if ($coupon) {
            $args['coupon_code'] = $coupon->coupon_code;
        }

        $order = new Order;
        $ret = $order->create($args);
        if (!$ret || $ret->error || !$order->id) {
            throw new Exception(_('無法建立訂單'));
        }

        /*
        if ($coupon) {
            $coupon->update(['used' => ['used + 1']]);
        }
        */
        foreach ($cart as $orderItem) {
            $orderItem->setAlias('oi');
            $ret = $orderItem->update([
                'order_id' => $this->record->id,
                'shipping_status' => 'unpaid',
            ]);
            if ($ret->error) {
                if ($ret->exception) {
                    throw $ret->exception;
                }
                throw new Exception("無法更新訂單項目: {$ret->message}");
            }
            if ($bundle->config('UseProductTypeQuantity')) {
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

