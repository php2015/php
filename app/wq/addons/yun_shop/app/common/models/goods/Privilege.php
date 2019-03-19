<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:54
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class Privilege extends BaseModel
{
    public $table = 'yz_goods_privilege';

    public $attributes = [
        'show_levels' => '',
        'show_groups' => '',
        'buy_levels' => '',
        'buy_groups' => '',
        'once_buy_limit' => 0,
        'total_buy_limit' => 0,
        'day_buy_limit' => 0,
        'week_buy_limit' => 0,
        'month_buy_limit' => 0,
        'time_begin_limit' => 0,
        'time_end_limit' => 0,
        'enable_time_limit' => 0

    ];
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];


    /**
     * 获取商品权限信息
     * @param int $goodsId
     * @return array  $goodsPrivilegeInfo
     */
    public static function getGoodsPrivilegeInfo($goodsId)
    {
        $goodsPrivilegeInfo = self::where('goods_id', $goodsId)
            ->first();
        return $goodsPrivilegeInfo;
    }


    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'show_levels' => '会员浏览等级',
            'show_groups' => '会员浏览分组',
            'buy_levels' => '会员购买等级',
            'buy_groups' => '会员购买分组',
            'once_buy_limit' => '单次购买限制',
            'total_buy_limit' => '总购买限制',
            'day_buy_limit' => '每天购买限制',
            'week_buy_limit' => '每周购买限制',
            'month_buy_limit' => '每月购买限制',
            'time_begin_limit' => '限时起始时间',
            'time_end_limit' => '限时结束时间',
        ];
    }


    public function rules()
    {
        return [
            'show_levels' => '',
            'show_groups' => '',
            'buy_levels' => '',
            'buy_groups' => '',
            'once_buy_limit' => 'numeric',
            'total_buy_limit' => 'numeric',
            'day_buy_limit' => 'numeric',
            'week_buy_limit' => 'numeric',
            'month_buy_limit' => 'numeric',
            'time_begin_limit' => '',
            'time_end_limit' => '',
        ];
    }

}