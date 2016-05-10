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

class CheckoutProcess
{
    protected $cart;

    protected $member;

    protected $productTypeQuantityEnabled = false;


    public function __construct(Member $member, Cart $cart)
    {
        $this->member = $member;
        $this->cart = $cart;
    }

    public function setProductTypeQuantityEnabled($enabled = true)
    {
        $this->productTypeQuantityEnabled = $enabled;
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
        $shippingFee     = $this->cart->calculateShippingFee();
        $origTotalAmount = $this->cart->calculateTotalAmount();
        $totalAmount     = $this->cart->calculateDiscountedTotalAmount();
        $discountAmount  = $this->cart->calculateDiscountAmount();

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

        // todo: update coupon used count
        $productType = new ProductType;
        $conn = $productType->getWriteConnection();
        foreach ($this->cart->getItems() as $orderItem) {
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
            if ($this->productTypeQuantityEnabled) {
                $this->updateProductTypeQuantity($orderItem);
            }
        }
        $this->postProcess($order);
        return true;
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
        $productType = $item->type;
        $conn = $productType->getWriteConnection();
        $conn->query('START TRANSACTION');
        $table = ProductType::TABLE;
        $checker = $conn->prepare("SELECT quantity FROM {$table} WHERE id = ? FOR UPDATE");
        $checker->execute([$item->type_id]);
        $result = $checker->fetch(PDO::FETCH_ASSOC);
        if (intval($result['quantity']) < $item->quantity) {
            // rollback if quantity update failed.
            $conn->query('ROLLBACK');
            return false;
        }

        $updater = $conn->prepare("UPDATE {$table} SET quantity = quantity - ? WHERE id = ?");
        $updater->execute([$item->quantity, $item->type_id]);
        $conn->query('COMMIT');
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

