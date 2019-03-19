<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\common\helpers\Client;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberMiniAppModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\McMappingFansModel;

class MemberMiniAppService extends MemberService
{
    const LOGIN_TYPE    = 2;  //小程序

    public function __construct()
    {}

    public function login()
    {
        include dirname(__FILE__ ) . "/../vendors/wechat/wxBizDataCrypt.php";

        $uniacid = \YunShop::app()->uniacid;

        $min_set = \Setting::get('plugin.min_app');

        if (is_null($min_set) || 0 == $min_set['switch']) {
            return show_json(0,'未开启小程序');
        }

        $para = \YunShop::request();

        $data = array(
            'appid' => $min_set['key'],
            'secret' => $min_set['secret'],
            'js_code' => $para['code'],
            'grant_type' => 'authorization_code',
        );

        $url = 'https://api.weixin.qq.com/sns/jscode2session';

        $user_info = \Curl::to($url)
            ->withData($data)
            ->asJsonResponse(true)
            ->get();
        
        $data = '';  //json

        if (!empty($para['info'])) {
            $json_data = json_decode($para['info'], true);

            $pc = new \WXBizDataCrypt($min_set['key'], $user_info['session_key']);
            $errCode = $pc->decryptData($json_data['encryptedData'], $json_data['iv'], $data);
        }

        if ($errCode == 0) {
            $json_user = json_decode($data, true);
        } else {
            return show_json(0,'登录认证失败');
        }

        if (!empty($json_user)) {
            if (isset($json_user['unionId'])) {
                $json_user['unionid']     = $json_user['unionId'];
            }

            $json_user['openid']     = $json_user['openId'];
            $json_user['nickname']   = $json_user['nickName'];
            $json_user['headimgurl'] = $json_user['avatarUrl'];
            $json_user['sex']        = $json_user['gender'];

            //Login
            $member_id = $this->memberLogin($json_user);
            //$this->createMiniMember($json_user, ['uniacid'=>$uniacid, 'member_id'=>$member_id]);

            Session::set('member_id', $member_id);

            $random = $this->wx_app_session($user_info);

            $result = array('session' => $random, 'wx_token' =>session_id(), 'uid' => $member_id);

            return show_json(1, $result);
        } else {
            return show_json(0, '获取用户信息失败');
        }
    }

    /**
     * 小程序登录态
     *
     * @param $user_info
     * @return string
     */
    function wx_app_session($user_info)
    {
        if (empty($user_info['session_key']) || empty($user_info['openid'])) {
            return show_json(0,'用户信息有误');
        }

        $random = md5(uniqid(mt_rand()));

        $_SESSION['wx_app'] = array($random => iserializer(array('session_key'=>$user_info['session_key'], 'openid'=>$user_info['openid'])));

        return $random;
    }

    public function createMiniMember($json_user, $arg)
    {
        $user_info = MemberMiniAppModel::getUserInfo($json_user['openid']);

        if (!empty($user_info)) {
            MemberMiniAppModel::updateUserInfo($json_user['openid'],array(
                'nickname' => $json_user['nickname'],
                'avatar' => $json_user['headimgurl'],
                'gender' => $json_user['sex'],
            ));
        } else {
            MemberMiniAppModel::insertData(array(
                'uniacid' => $arg['uniacid'],
                'member_id' => $arg['member_id'],
                'openid' => $json_user['openid'],
                'nickname' => $json_user['nickname'],
                'avatar' => $json_user['headimgurl'],
                'gender' => $json_user['sex'],
            ));
        }
    }

    /**
     * 公众号开放平台授权登陆
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function unionidLogin($uniacid, $userinfo, $upperMemberId = NULL)
    {
        $member_id = parent::unionidLogin($uniacid, $userinfo, $upperMemberId = NULL, self::LOGIN_TYPE);

        return $member_id;
    }

    /**
     * 小程序平台授权登陆
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    /*public function openidLogin($uniacid, $userinfo, $upperMemberId = NULL)
    {
        $member_id = 0;
        $userinfo['nickname'] = $this->filteNickname($userinfo);
        $fans_mode = MemberMiniAppModel::getUId($userinfo['openid']);

        if ($fans_mode) {
            $member_model = Member::getMemberById($fans_mode->uid);
            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($fans_mode->uid);

            $member_id = $fans_mode->uid;
        }

        if ((!empty($member_model)) && (!empty($fans_mode) && !empty($member_shop_info_model))) {
            \Log::debug('小程序登陆更新');

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            if (empty($member_model) && empty($fans_mode)) {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);

                if ($member_id === false) {
                    return show_json(8, '保存用户信息失败');
                }
            } elseif ($fans_mode->uid) {
                $member_id = $fans_mode->uid;

                $this->updateMemberInfo($member_id, $userinfo);
            }

            if (empty($member_shop_info_model)) {
                $this->addSubMemberInfo($uniacid, $member_id);
            }

            //生成分销关系链
            if ($upperMemberId) {
                \Log::debug('分销关系链-海报');
                Member::createRealtion($member_id, $upperMemberId);
            } else {
                \Log::debug('分销关系链-链接');
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }*/

    public function updateMemberInfo($member_id, $userinfo)
    {
        parent::updateMemberInfo($member_id, $userinfo);

        $record = array(
            'openid' => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname'])
        );

        MemberMiniAppModel::updateData($member_id, $record);
    }

    public function addMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        //$this->addMcMemberFans($uid, $uniacid, $userinfo);
        $this->addFansMember($uid, $uniacid, $userinfo);

        return $uid;
    }

    public function addMcMemberFans($uid, $uniacid, $userinfo)
    {
        McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));
    }

    public function addFansMember($uid, $uniacid, $userinfo)
    {
        MemberMiniAppModel::insertData(array(
            'uniacid' => $uniacid,
            'member_id' => $uid,
            'openid' => $userinfo['openid'],
            'nickname' => $userinfo['nickname'],
            'avatar' => $userinfo['headimgurl'],
            'gender' => $userinfo['sex'],
        ));
    }

    public function getFansModel($openid)
    {
        $model = MemberMiniAppModel::getUId($openid);

        if (!is_null($model)) {
            $model->uid = $model->member_id;
        }

        return $model;
    }

    /**
     * 会员关联表操作
     *
     * @param $uniacid
     * @param $member_id
     * @param $unionid
     */
    public function addMemberUnionid($uniacid, $member_id, $unionid)
    {
        MemberUniqueModel::insertData(array(
            'uniacid' => $uniacid,
            'unionid' => $unionid,
            'member_id' => $member_id,
            'type' => self::LOGIN_TYPE
        ));
    }
}
