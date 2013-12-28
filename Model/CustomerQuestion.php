<?php
namespace CartBundle\Model;

class CustomerQuestion  extends \CartBundle\Model\CustomerQuestionBase {

    public function beforeUpdate($args) {
        if ( isset($args['question']) && $args['question'] ) {
            $args['question_time'] = date('c');
        }
        return $args;
    }

}
