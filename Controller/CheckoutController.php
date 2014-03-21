<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use Exception;
use CartBundle\Cart;
use CartBundle\Model\Order;
use CartBundle\Controller\NewebPaymentController;
use StoreLocationBundle\Model\StoreCategory;

class CheckoutController extends OrderBaseController
{

    public function confirmAction() {
        $cart = Cart::getInstance();
        $cart->purgeQuantityInvalidItems();
        $orderItems = $cart->getOrderItems();
        if ( ! $orderItems || empty($orderItems) ) {
            return $this->redirect('/cart');
        }
        return $this->render("checkout_confirm.html", [
        ]);
    }


    public function orderAction() {
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        if ( ! $orderItems || empty($orderItems) ) {
            return $this->redirect('/cart');
        }

        $stores = null;
        $storeCategory = new StoreCategory;
        $storeCategory->load(array('handle' => 'delivery'));
        if ( $storeCategory->id ) {
            $stores = $storeCategory->stores;
        }
        return $this->render("checkout_order.html",array( 
            'delivery_stores' => $stores,
        ));
    }

}
