<?php
namespace CartBundle\Controller;
use Phifty\Controller;

class CartController extends Controller
{
    public function indexAction() {
        return $this->render("cart.html");
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
