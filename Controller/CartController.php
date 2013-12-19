<?php
namespace CartBundle\Controller;
use Phifty\Controller;
use CartBundle\Cart;

class CartController extends Controller
{

    public function indexAction() {
        $cart = Cart::getInstance();
        $orderItems = $cart->getOrderItems();
        return $this->render("cart.html",array(
            'cart' => $cart,
            'orderItems' => $orderItems,
        ));
    }

    public function calculateAction() {

    }

    public function applyCouponAction() {

    }


    /**
     * @param $id               integer order item id.
     * @param $product_type     integer product type id
     * @param $quantity integer quantity
     */
    public function updateItem($id, $quantity)
    {

    }

}
