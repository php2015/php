<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/24
 * Time: 下午2:29
 */

namespace app\common\services;


class CashPay extends Pay
{
    public function __construct()
    {
    }

    public function doPay($data = [])
    {
        //pay.php 980
        //支付单
        //订单 支付类型
         // TODO 修改订单状态
    }

    public function doRefund($out_trade_no, $out_refund_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}