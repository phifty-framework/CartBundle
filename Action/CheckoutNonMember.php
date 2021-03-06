<?php

namespace CartBundle\Action;

use MemberBundle\CurrentMember;

class CheckoutNonMember extends Checkout
{
    public function schema()
    {
        $this->param('email')
            ->required()
            ->label(_('E-mail'))
            ;

        $this->param('password')
            ->required()
            ->label(_('密碼'))
            ;

        $this->param('password_confirm')
            ->required()
            ->label(_('確認密碼'))
            ;

        parent::schema();
    }

    public function run()
    {
        if ($this->arg('password') != $this->arg('password_confirm')) {
            return $this->error(_('您所輸入的密碼不同。'));
        }

        // copy buyer_fields to member fields
        $fields = array('buyer_name', 'buyer_phone', 'buyer_cellphone', 'buyer_address', 'buyer_gender');
        foreach ($fields as $field) {
            $memberField = str_replace('buyer_', '', $field);
            $this->setArgument($memberField, $this->arg($field));
        }

        $member = new \MemberBundle\Model\Member();

        $member->load(array('email' => $this->arg('email')));
        if ($member->id) {
            return $this->error(_('此 Email 已經註冊過了'));
        }

        $ret = $member->create(array(
            'name' => $this->arg('name'),
            'phone' => $this->arg('phone'),
            'cellphone' => $this->arg('cellphone'),
            'email' => $this->arg('email'),
            'gender' => $this->arg('gender'),
            'address' => $this->arg('address'),
            'password' => sha1($this->arg('password')),
        ));
        if (!$ret->success) {
            return $this->error(_('系統發生了問題，請聯絡客服。謝謝您。'));
        }
        $currentMember = new CurrentMember();
        $currentMember->setRecord($member);
        $this->setArgument('member_id', $member->id);

        return parent::run();
    }
}
