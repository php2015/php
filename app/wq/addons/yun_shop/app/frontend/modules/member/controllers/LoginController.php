<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Member;
use app\frontend\modules\member\services\factory\MemberFactory;

class LoginController extends ApiController
{
    protected $publicController = ['Login'];
    protected $publicAction = ['index', 'phoneSetGet'];
    protected $ignoreAction = ['index', 'phoneSetGet'];

    public function index()
    {
        $type = \YunShop::request()->type ;

        if (empty($type) || $type == 'undefined') {
            $type = Client::getType();
        }

        //判断是否开启微信登录
        if (\YunShop::request()->show_wechat_login) {
            return $this->init_login();
        }
      
        if (!empty($type)) {
                $member = MemberFactory::create($type);

                if ($member !== NULL) {
                    $msg = $member->login();

                    if (!empty($msg)) {
                        if ($msg['status'] == 1) {
                            $uniacid = \YunShop::app()->uniacid;
                            $mid = Member::getMid();
                            $url = Url::absoluteApp('member', ['i' => $uniacid, 'mid' => $mid]);
                            return $this->successJson($msg['json'], ['status'=> $msg['status'], 'url' => $url]);
                        } else {
                            if ($msg['status'] == -3) {
                                return view('errors.login',['title'=>$msg['json'], 'content'=>$msg['json']])->render();
                            }

                            return $this->errorJson($msg['json'], ['status'=> $msg['status']]);
                        }
                    } else {
                        return $this->errorJson('登录失败', ['status' => 3]);
                    }
                } else {
                    return $this->errorJson('登录异常', ['status'=> 2]);
                }
        } else {
            return $this->errorJson('客户端类型错误', ['status'=> 0]);
        }
    }

    /**
     * 初始化登录，判断是否开启微信登录
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function init_login () {
        $weixin_oauth = \Setting::get('shop_app.pay.weixin_oauth');
        return $this->successJson('', ['status'=> 1, 'wetach_login' => $weixin_oauth]);
    }

    public function phoneSetGet()
    {
        $phone_oauth = \Setting::get('shop_app.pay.phone_oauth');

        if (empty($phone_oauth)) {
            $phone_oauth = '0';
        }
        return $this->successJson('ok', ['phone_oauth' => $phone_oauth]);
    }
}