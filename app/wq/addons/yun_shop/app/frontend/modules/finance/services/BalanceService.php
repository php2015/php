<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午7:01
 */

namespace app\frontend\modules\finance\services;

use app\common\exceptions\AppException;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\facades\Setting;
use app\frontend\modules\finance\models\BalanceRecharge;

class BalanceService
{
    private $_recharge_set;

    private $_withdraw_set;

    public function __construct()
    {
        $this->_recharge_set = Setting::get('finance.balance');
        $this->_withdraw_set = Setting::get('withdraw.balance');
    }


    //余额设置接口
    public function getBalanceSet()
    {
        return array(
            'recharge'          => $this->_recharge_set['recharge'] ? 1 : 0,
            'transfer'          => $this->_recharge_set['transfer'] ? 1 : 0,
            'withdraw'          => $this->_withdraw_set['status'] ? 1 : 0,
            'withdrawToWechat'  => $this->withdrawWechat(),
            'withdrawToAlipay'  => $this->withdrawAlipay(),
            'withdrawToManual'  => $this->withdrawManual()
        );
    }

    //余额充值设置
    public function rechargeSet()
    {
        return $this->_recharge_set['recharge'] ? true : false;
    }

    //余额充值优惠
    public function rechargeSale()
    {
        return $this->rechargeSet() ? $this->_recharge_set['sale'] : [];
    }

    //0赠送固定金额，1赠送充值比例
    
    public function proportionStatus()
    {
        return isset($this->_recharge_set['proportion_status']) ? $this->_recharge_set['proportion_status'] : '0';
    }

    //余额转让设置
    public function transferSet()
    {
        return $this->_recharge_set['transfer'] ? true : false;
    }

    //余额提现设置
    public function withdrawSet()
    {
        return $this->_withdraw_set['status'] ? true : false;
    }

    //余额提现限额设置
    public function withdrawAstrict()
    {
        return $this->_withdraw_set['withdrawmoney'] ?: '0';
    }

    //余额提现手续费
    public function withdrawPoundage()
    {
        return $this->_withdraw_set['poundage'] ?: '0';
    }

    //余额提现到微信
    public function withdrawWechat()
    {
        return $this->_withdraw_set['wechat'] ? true : false;
    }

    //余额提现到支付宝
    public function withdrawAlipay()
    {
        return $this->_withdraw_set['alipay'] ? true : false;
    }

    //余额手动提现
    public function withdrawManual()
    {
        return $this->_withdraw_set['balance_manual'] ? true : false;
    }


    /**
     * 提现满 N元 减免手续费 [注意为 0， 为空则不计算，按正常手续费扣]
     * 2017-09-28
     * @return string
     */
    public function withdrawPoundageFullCut()
    {
        return $this->_withdraw_set['poundage_full_cut'] ?: '0';
    }


    /**
     * 增加提现手续费类型，1固定金额，0（默认）手续费比例
     * 2017-09-28
     * @return int
     */
    public function withdrawPoundageType()
    {
        return $this->_withdraw_set['poundage_type'] ? 1 : 0;
    }

    public function rechargeActivityStatus()
    {
        return $this->_recharge_set['recharge_activity'] ? true : false;
    }

    public function rechargeActivityStartTime()
    {
        return $this->_recharge_set['recharge_activity_start'] ?: 0;
    }

    public function rechargeActivityEndTime()
    {
        return $this->_recharge_set['recharge_activity_end'] ?: 0;
    }

    public function rechargeActivityCount()
    {
        return $this->_recharge_set['recharge_activity_count'] ?: 1;
    }

    public function rechargeActivityFetter()
    {
        return $this->_recharge_set['recharge_activity_fetter'];
    }


}
