<?php
namespace CartBundle\Process;
use CartBundle\Cart;
use CartBundle\Model\Order;
use CartBundle\Model\OrderItem;
use CartBundle\Email\OrderCreatedEmail;
use CartBundle\CartBundle;
use ProductBundle\Model\ProductType;
use MemberBundle\Model\Member;
use Exception;
use PDO;

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

class InsufficientOrderItemQuantityException extends CheckoutException
{
    protected $orderItem;

    protected $availableQuantity;

    public function __construct(OrderItem $orderItem, $availableQuantity, $message)
    {
        $this->orderItem = $orderItem;
        $this->availableQuantity = $availableQuantity;
        parent::__construct($message);
    }

}

class CheckoutProcess
{
    protected $cart;

    protected $member;

    protected $productTypeQuantityEnabled = false;

    protected $baseAmount = 0;

    public function __construct(Member $member, Cart $cart)
    {
        $this->member = $member;
        $this->cart = $cart;
    }

    public function setBaseAmount($baseAmount)
    {
        $this->baseAmount = $baseAmount;
    }

    public function setExtraItems(array $extraItems)
    {
        $this->extraItems = $extraItems;
    }

    public function setProductTypeQuantityEnabled($enabled = true)
    {
        $this->productTypeQuantityEnabled = $enabled;
    }

    public function preprocess()
    {
        $this->cart->removeInvalidItems(true, true);
    }


    public function createOrder(array $formInputs)
    {
        // preprocess with cart items
        $baseAmount = $this->baseAmount ?: 0;
        $shippingFee     = $this->cart->calculateShippingFee();
        $origTotalAmount = $this->cart->calculateTotalAmount();
        $discountAmount  = $this->cart->calculateDiscountAmount();
        $totalAmount     = $this->cart->calculateDiscountedTotalAmount();

        // Calculate extra fee from extraItems
        $extraAmount = 0;
        if (!empty($this->extraItems)) {
            /*
            $extraItem = [
                'className' => 'event-group-fee',
                'label' => "組別 {$eventReg->group->title}",
                'price' => $eventReg->group->fee
            ];
            */
            $extraAmount = array_reduce($this->extraItems, function($carry, $current) {
                if (isset($current['price'])) {
                    return $carry + intval($current['price']);
                }
                return $carry;
            }, 0);
        }

        // Use Try-Cache to cache exceptions and process fallbacks.
        $formInputs['paid_amount']     = 0;
        $formInputs['shipping_fee']    = $shippingFee;
        $formInputs['discount_amount'] = $discountAmount;
        $formInputs['total_amount']    = $baseAmount + $extraAmount + $totalAmount;
        $formInputs['member_id']       = $this->member->id;

        if ($coupon = $this->cart->getCurrentCoupon()) {
            $formInputs['coupon_code'] = $coupon->coupon_code;
        }

        $order = new Order;
        $ret = $order->create($formInputs);
        if (!$ret || $ret->error || !$order->id) {
            throw new InvalidOrderFormException(_('無法建立訂單'), $ret);
        }
        return $order;
    }


    protected function updateCouponStatus($couponCode)
    {
        // todo:
    }



    /**
     * The checkout method creates an order base on the given cart items.
     *
     * If the order was successfully created, then the Order object will be returned.
     *
     * @param array $formInputs argument array contains basic information.
     * @return CartBundle\Model\Order
     */
    public function checkout(array $formInputs)
    {
        $order = $this->createOrder($formInputs);
        if ($orderItems = $this->cart->getItems()) {
            foreach ($orderItems as $orderItem) {
                $this->updateOrderItemStatus($orderItem, $order);
                if ($this->productTypeQuantityEnabled) {
                    $this->updateProductTypeQuantity($orderItem);
                }
            }
        }
        // fixme: read coupon code from somewhere...
        if (isset($formInputs['coupon_code'])) {
            $this->updateCouponStatus($formInputs['coupon_code']);
        }
        $this->postProcess($order);
        return $order;
    }

    /**
     * Checkout process with transaction handling
     *
     * @param PDO $conn connection for transaction
     * @param array $formInputs
     */
    public function checkoutWithTransaction(PDO $conn, array $formInputs)
    {
        $conn->query('START TRANSACTION');
        try {
            $order = $this->checkout($formInputs);
            $conn->query('COMMIT');
            return $order;
        } catch (Exception $e) {
            $conn->query('ROLLBACK');
            throw $e;
        }
        return false;
    }

    protected function updateOrderItemStatus(OrderItem $item, Order $order)
    {
        $ret = $item->update([
            'order_id'        => $order->id,
            'delivery_status' => 'unpaid',
        ]);
        if ($ret->error) {
            if ($ret->exception) {
                throw $ret->exception;
            }
            throw new CheckoutException("無法更新訂單項目: {$ret->message}");
        }
    }

    /**
     * Update product type quantity base on the item quantity
     *
     * @return boolean
     */
    protected function updateProductTypeQuantity(OrderItem $item)
    {
        if (!$item->type_id) {
            return false;
        }
        $conn = $item->getWriteConnection();
        $table = ProductType::TABLE;
        $checker = $conn->prepare("SELECT quantity FROM {$table} WHERE id = ? FOR UPDATE");
        $checker->execute([$item->type_id]);
        $result = $checker->fetch(PDO::FETCH_ASSOC);
        if (intval($result['quantity']) < $item->quantity) {
            // rollback if quantity update failed.
            throw new InsufficientOrderItemQuantityException($item, intval($result['quantity']), "quantity is not enough.");
        }

        $updater = $conn->prepare("UPDATE {$table} SET quantity = quantity - ? WHERE id = ?");
        $updater->execute([$item->quantity, $item->type_id]);
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

