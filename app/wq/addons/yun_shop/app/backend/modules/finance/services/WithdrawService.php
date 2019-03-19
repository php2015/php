<?php
namespace app\backend\modules\finance\services;

use app\backend\modules\finance\services\BalanceService;
use app\common\exceptions\AppException;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\Withdraw;
use app\common\services\PayFactory;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/31
 * Time: 下午3:13
 */
class WithdrawService extends Withdraw
{
    public static function createStatusService($withdraw)
    {

        switch ($withdraw->status) {
            case -1:
                return '无效';
                break;
            case 0:
                return '未审核';
                break;
            case 1:
                return '未打款';
                break;
            case 2:
                return '已打款';
                break;
            case 3:
                return '驳回';
                break;

        }
    }

    public static function balanceWithdrawPay($withdraw, $remark)
    {
        $data = array(
           /* 'member_id' => $withdraw->member_id,
            'money' => $withdraw->actual_amounts,
            'serial_number' => '',
            'operator' => '-2',
            'operator_id' => $withdraw->id,
            'remark' => $remark,
            'service_type' => \app\common\models\finance\Balance::BALANCE_INCOME,*/

            'member_id'     => $withdraw->member_id,
            'remark'        => $remark,
            'source'        => ConstService::SOURCE_INCOME,
            'relation'      => '',
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $withdraw->id,
            'change_value'  => $withdraw->actual_amounts
        );
        return (new BalanceChange())->income($data);
    }

    public static function wechatWithdrawPay($withdraw, $remark)
    {
        return  PayFactory::create(1)->doWithdraw($withdraw->member_id, $withdraw->withdraw_sn, $withdraw->actual_amounts, $remark);
    }

    public static function alipayWithdrawPay($withdraw, $remark)
    {
        if (is_array($withdraw)) {
            $result = PayFactory::create(2)->doBatchWithdraw($withdraw);
        } else {
            $result = PayFactory::create(2)->doWithdraw($withdraw->member_id, $withdraw->withdraw_sn, $withdraw->actual_amounts, $remark);
        }

        if (is_array($result)) {
            return $result;
        }

        redirect($result)->send();
    }
}