<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 下午3:28
 */

namespace app\frontend\modules\finance\models;

use app\common\models\VirtualCoin;

class PointCoin extends VirtualCoin
{
    function __construct(array $attribute = [])
    {
        parent::__construct($attribute);
    }

    protected function _getExchangeRate()
    {
        return \Setting::get('point.set.money',0);
    }

    protected function _getName()
    {
        $credit1 = trim(\Setting::get('shop.shop.credit1'));

        return $credit1 ? $credit1 : '积分';
        // return \Setting::get('shop.shop.credit1','积分');
    }

    protected function _getCode()
    {
        return 'point';
    }
}