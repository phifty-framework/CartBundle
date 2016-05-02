<?php

namespace CartBundle\Model;

use LazyRecord\Schema\SchemaDeclare;

class CustomerQuestionSchema extends SchemaDeclare
{
    public function schema()
    {
        $this->column('question_title')
            ->varchar(120)
            ->label('問題主旨')
            ->required()
            ->renderAs('TextInput', array('size' => 50))
            ;

        $this->column('question')
            ->text()
            ->label('問題')
            ->required()
            ->renderAs('TextareaInput', array(
                'rows' => 5,
                'cols' => 50,
            ))
            ;

        $this->column('question_time')
            ->timestamp()
            ->null()
            ->renderAs('DateTimeInput')
            ->label(_('發問時間'))
            ->default(function () {
                return date('c');
            })
            ;

        $this->column('answer')
            ->text()
            ->label('客服回答問題')
            ->renderAs('TextareaInput', array(
                'rows' => 5,
                'cols' => 50,
            ))
            ;

        $this->column('answer_time')
            ->timestamp()
            ->null()
            ->renderAs('DateTimeInput')
            ->label(_('回答時間'))
            ;

        $this->column('order_id')
            ->integer()
            ->refer('CartBundle\\Model\\OrderSchema')
            ->label('訂單')
            ;

        $this->column('order_item_id')
            ->integer()
            ->refer('CartBundle\\Model\\OrderItemSchema')
            ->label('訂單項目')
            ;

        $this->column('member_id')
            ->integer()
            ->refer('MemberBundle\\Model\\MemberSchema')
            ->default(function () {
                if (isset($_SESSION)) {
                    $currentMember = new \MemberBundle\CurrentMember();

                    return $currentMember->id;
                }
            })
            ->renderAs('SelectInput')
            ->label('會員')
            ;

        $this->column('remark')
            ->text()
            ->label('後台備註')
            ->renderAs('TextareaInput', ['rows' => 3, 'cols' => 40])
            ;

        $this->belongsTo('member', 'MemberBundle\\Model\\MemberSchema', 'id', 'member_id');

        $this->belongsTo('order', 'CartBundle\\Model\\OrderSchema', 'id', 'order_id');

        $this->belongsTo('order_item', 'CartBundle\\Model\\OrderItemSchema', 'id', 'order_item_id');

        $this->mixin('CommonBundle\\Model\\Mixin\\MetaSchema');
    }
}
