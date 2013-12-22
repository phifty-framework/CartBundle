<?php
namespace CartBundle;
use Phifty\Bundle;

class CartBundle extends Bundle
{
    public function assets() { return array(); }

    public function defaultConfig() { return array(); }

    public function init() 
    {
        $this->route('/=/cart/items', 'CartController:items');
        $this->route('/=/cart/calculate', 'CartController:calculate');
        $this->route('/=/cart/apply_coupon', 'CartController:applyCoupon');


        $this->route('/cart', 'CartController:index');
        $this->route('/checkout/confirm', 'CheckoutController:confirm');
        $this->route('/checkout/review', 'CheckoutController:review');
        $this->route('/checkout/order', 'CheckoutController:order');
        $this->route('/checkout/payment', 'CheckoutController:payment');

        $this->route('/payment/neweb', 'NewebPaymentController:neweb');
        $this->route('/payment/neweb/response', 'NewebPaymentController:newebResponse');
        $this->route('/payment/neweb/return', 'NewebPaymentController:newebReturn');

        /*
        $this->expandRoute( '/bs/product_resource', '\\Product\\ProductResourceCRUDHandler' );
        $this->expandRoute( '/bs/product_image' ,   '\\Product\\ProductImageCRUDHandler' );
        $this->expandRoute( '/bs/product_file' ,    '\\Product\\ProductFileCRUDHandler' );

        if ( $this->config('with_agency_products') ) {
            $this->expandRoute( '/bs/agency_product', '\\Product\\AgencyProductCRUD' );
        }
        if ( $this->config('with_types') ) {
            $this->expandRoute( '/bs/product_type', '\\Product\\ProductTypeCRUDHandler' );
        }

        $this->route( '/bs/product/spec_panel', 'SpecPanelController');
        $this->route( '/bs/product/api/delete_spec/{schemaId}' , 'SpecDataController:deleteSchemaAndData' );

        // save/add spec data (item)
        // $this->route( '/bs/product/api/save_spec_data', 'SpecDataController:saveSpecData');

        $this->addCRUDAction( 'Category' , array('Create','Update','Delete','BulkDelete') );
        $this->addCRUDAction( 'Product' , array('BulkDelete') );
        $this->addCRUDAction( 'ProductType' , array('Create','Update','Delete','BulkDelete') );
        $this->addCRUDAction( 'Feature' , array('Delete') );
        $this->addCRUDAction( 'FeatureRel' , array('Create','Update','Delete') );
        $this->addCRUDAction( 'Resource' , array('Create','Update','Delete') );
        */
    }

}
