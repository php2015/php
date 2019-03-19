<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 上午11:44
 */

namespace app\common\events\finance;


use app\common\events\Event;

class IncomeWithdrawEvent extends Event
{
    protected $withdrawData;
    
    public function __construct($withdrawData)
    {
        $this->withdrawData = $withdrawData;
    }
    
    public function getData()
    {
        return $this->withdrawData['withdrawal'];
    }

    public function getWithdrawTotal()
    {
        return $this->withdrawData['total'];
    }
}
