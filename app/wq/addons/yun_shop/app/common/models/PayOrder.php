<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 上午10:42
 */

namespace app\common\models;

use app\backend\models\BackendModel;

class PayOrder extends BackendModel
{
    public $table = 'yz_pay_order';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['uniacid', 'member_id', 'int_order_no', 'out_order_no', 'status', 'type', 'third_type', 'price'];

    public static function getPayOrderInfo($orderno)
    {
        return self::uniacid()
            ->where('out_order_no', $orderno)
            ->orderBy('id', 'desc');
    }

    public static function getPayOrderInfoByTradeNo($trade_no)
    {
        return self::uniacid()
            ->where('trade_no', $trade_no)
            ->orderBy('id', 'desc');
    }
}