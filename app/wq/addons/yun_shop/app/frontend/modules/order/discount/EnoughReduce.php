<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;

class EnoughReduce extends BaseDiscount
{
    protected $code = 'enoughReduce';
    protected $name = '全场满减优惠';
    /**
     * 获取总金额
     * @return float
     */
    protected function _getAmount()
    {
        //只有商城,供应商订单参加
        if($this->order->plugin_id != 0){
            return 0;
        }
        // 获取满减设置,按enough倒序
        $settings = collect(\Setting::get('enoughReduce.enoughReduce'));
//dump($settings);
        if (empty($settings)) {
            return 0;
        }

        $settings = $settings->sortByDesc(function ($setting) {
            return $setting['enough'];
        });

        // 订单总价满足金额,则返回优惠金额
        foreach ($settings as $setting) {

            if ($this->order->price >= $setting['enough']) {
                return min($setting['reduce'],$this->order->price);
            }
        }
        return 0;
    }
}