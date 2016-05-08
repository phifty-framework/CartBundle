<?php

namespace CartBundle\Model;

use DateTime;

class OrderCollection  extends \CartBundle\Model\OrderCollectionBase
{
    public static function getCountByDay($date)
    {
        $orders = new self();
        $orders->where(['date_format(created_on,\'%Y-%m-%d\')' => $date instanceof DateTime ? $date->format('Y-m-d') : $date]);
        return $orders->size();
    }
}
