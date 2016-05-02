<?php

namespace CartBundle\Controller;

use Phifty\Controller;
use CartBundle\Cart;

class CartController extends Controller
{
    /**
     * Cart page controller action.
     */
    public function indexAction()
    {
        $cart = Cart::getInstance();
        // $cart->validateItems();
        return $this->render('cart.html', ['cart' => $cart]);
    }
}
