<?php
namespace CartBundle\Action;
use ActionKit\Action;

class AddToCart extends Action
{
    public function schema() {
        $this->param('product_id')
            ->required()
            ->label( _('產品') );

        $this->param('quantity')
            ->required()
            ->label( _('數量') );

        $this->param('product_type')
            ->required()
            ->label( _('類型') );
    }

    public function run() {
        return $this->success('成功新增至購物車');
    }
}
