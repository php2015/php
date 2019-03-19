<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/15
 * Time: 上午8:56
 */

namespace app\common\services\credit;


abstract class Credit
{
    protected $data =[];

    protected $type = ConstService::TYPE_INCOME;

    protected $source;

    protected $change_value;



    protected $memberModel;

    //abstract function changeValue();

    abstract function getMemberModel();

    abstract function recordSave();

    abstract function updateMemberCredit();

    abstract function validatorData();


    /**
     * 充值接口
     * @param array $data
     * @return string
     */
    public function recharge(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE;
        return $this->addition($data);
    }

    /**
     * 后台扣除
     * @param array $data
     * @return string
     */
    public function rechargeMinus(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE_MINUS;
        return $this->subtraction($data);
    }

    /**
     * 消费接口
     * @param array $data
     * @return string
     */
    public function consume(array $data)
    {
        $this->source = ConstService::SOURCE_CONSUME;
        return $this->subtraction($data);
    }

    /**
     * 转让接口
     * @param array $data
     * @return string
     */
    public function transfer(array $data)
    {
        $this->source = ConstService::SOURCE_TRANSFER;
        return $this->subtraction($data);
    }

    /**
     * 抵扣接口
     * @param array $data
     * @return string
     */
    public function deduction(array $data)
    {
        $this->source = ConstService::SOURCE_DEDUCTION;
        return $this->subtraction($data);
    }

    /**
     * 奖励接口
     * @param array $data
     * @return string
     */
    public function award(array $data)
    {
        $this->source = ConstService::SOURCE_AWARD;
        return $this->addition($data);
    }

    /**
     * 提现接口
     * @param array $data
     * @return string
     */
    public function withdrawal(array $data)
    {
        $this->source = ConstService::SOURCE_WITHDRAWAL;
        return $this->subtraction($data);
    }

    /**
     * 提现至………（余额）………接口
     * @param array $data
     * @return string
     */
    public function income(array $data)
    {
        $this->source = ConstService::SOURCE_INCOME;
        return $this->addition($data);
    }

    /**
     * 抵扣取消回滚接口
     * @param array $data
     * @return string
     */
    public function cancelDeduction(array $data)
    {
        $this->source = ConstService::SOURCE_CANCEL_DEDUCTION;
        return $this->addition($data);
    }

    /**
     * 奖励取消回滚接口
     * @param array $data
     * @return string
     */
    public function cancelAward(array $data)
    {
        $this->source = ConstService::SOURCE_CANCEL_AWARD;
        return $this->subtraction($data);
    }

    /**
     * 消费取消回滚接口
     * @param array $data
     * @return string
     */
    public function cancelConsume(array $data)
    {
        $this->source = ConstService::SOURCE_CANCEL_CONSUME;
        return $this->addition($data);
    }


    //加法
    protected function addition($data)
    {
        if (!$data['change_value'] || $data['change_value'] < 0) {
            return '变动值必须是正数';
        }
        $this->data = $data;
        $this->type = ConstService::TYPE_INCOME;
        $this->change_value = $this->data['change_value'];

        return $this->result();
    }

    //减法
    protected function subtraction($data)
    {
        if (!$data['change_value'] || $data['change_value'] < 0) {
            return '变动值必须是正数';
        }
        $this->data = $data;
        $this->type = ConstService::TYPE_EXPENDITURE;
        $this->change_value = -$this->data['change_value'];

        return $this->result();
    }


    protected function result()
    {
        $this->memberModel = $this->getMemberModel();
        if (!$this->memberModel) {
            return '未获取到会员数据';
        }
        $validator = $this->validatorData();
        if (!($validator === true)) {
            return $validator;
        }

        $result = $this->recordSave();
        return $result === true ? $this->updateMemberCredit() : $result;
    }


}
