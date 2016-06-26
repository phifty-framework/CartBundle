<?php
namespace CartBundle\Process;
use CartBundle\Cart;
use CartBundle\Model\Order;
use CartBundle\Model\Coupon;
use CartBundle\Model\OrderItem;
use CartBundle\Email\OrderCreatedEmail;
use CartBundle\CartBundle;
use ProductBundle\Model\ProductType;
use ProductBundle\Exception\InsufficientTypeQuantityException;
use MemberBundle\Model\Member;
use Exception;
use RuntimeException;
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

    public function __construct(OrderItem $orderItem, $message = null, $availableQuantity = null)
    {
        $this->orderItem = $orderItem;
        parent::__construct($message);
        $this->availableQuantity = $availableQuantity;
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
        if (empty($couponCode)) {
            return false;
        }

        $coupon = new Coupon;
        $ret = $coupon->load([ 'code' => $couponCode ]);
        if ($ret->error) {
            throw new Exception("Inexistent coupon");
        }
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
            // TODO: Simplify this to single query?
            foreach ($orderItems as $orderItem) {
                $this->updateOrderItemStatus($orderItem, $order);
                if ($this->productTypeQuantityEnabled) {
                    $this->decuctOrderItemQuantity($orderItem);
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

    /**
     * Method method will modify the order_id field and update the delivery_status to 'unpaid'.
     */
    protected function updateOrderItemStatus(OrderItem $item, Order $order)
    {
        $ret = $item->update([
            'order_id'        => $order->id,
            'delivery_status' => 'unpaid',
        ]);
        if ($ret->error) {
            throw new CheckoutException("無法更新訂單項目: {$ret->message}");
        }
    }

    /**
     * Update product type quantity base on the item quantity
     *
     * @return boolean
     */
    protected function decuctOrderItemQuantity(OrderItem $item)
    {
        if (!$item->satisfyQuantity()) {
            throw new InsufficientOrderItemQuantityException($item, "quantity is not enough.");
        }

        try {
            $conn = $item->getWriteConnection();
            $conn->query('BEGIN');
            $item->deductQuantity($item->quantity, $conn);
            $conn->query('COMMIT');
        } catch (InsufficientTypeQuantityException $e) {
            $conn->query('ROLLBACK');
            throw new InsufficientOrderItemQuantityException($item, "quantity is not enough.", $e->getActualQuantity());
        } catch (Exception $e) {
            $conn->query('ROLLBACK');
        }
        return true;
    }


    /**
     * Trigger post process
     */
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

