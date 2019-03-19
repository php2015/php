<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/8
 * Time: 下午5:49
 */

namespace app\common\services\finance;


use app\common\facades\Setting;

class IncomeService
{
    private static $pay_way = ['balance','wechat','alipay','manual'];

    public static function getIncomeWithdrawMode()
    {
        $set = Setting::get('withdraw.income');
        $modeData = [];
        foreach ($set as $key => $item) {
            if(in_array($key, static::$pay_way) && $item){
                $modeData[$key] = [
                    'name' => self::getModeName($key),
                    'value' => $key
                ];
            }
        }
        return $modeData;
    }

    public static function getModeName($key)
    {
        switch ($key) {
            case 'balance':
                return '提现到余额';
                break;
            case 'wechat':
                return '提现到微信';
                break;
            case 'alipay':
                return '提现到支付宝';
                break;
            case 'manual':
                return '提现手动打款';
                break;
        }
    }


}