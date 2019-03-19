<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 上午11:42
 */

namespace app\common\models;

class Sale extends BaseModel
{
    public $table = 'yz_goods_sale';

    public $attributes = [
        'max_point_deduct' => '',
        'max_balance_deduct' => 0,
        'is_sendfree' => 0,
        'ed_num' => 0,
        'ed_full' => 0,
        'ed_reduction' => 0,
        'ed_money' => 0,
        'point' => '',
        'bonus' => 0
    ];

    protected $guarded = [''];

    protected $fillable = [''];
}