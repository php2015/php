<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午4:36
 */

namespace app\common\exceptions;

use Exception;


class NotFoundException extends Exception
{
    public function getStatusCode()
    {
        return 404;
    }

    public function getHeaders()
    {
        return [];
    }
}