<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 下午4:53
 */

/**
 * 会员表
 */
namespace app\frontend\modules\member\models;

use app\backend\modules\member\models\MemberRelation;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\Setting;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use app\common\models\Order;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Merchant\common\models\MerchantLevel;
use Yunshop\Micro\common\models\MicroShopLevel;
use Yunshop\TeamDividend\models\TeamDividendLevelModel;

class MemberModel extends Member
{
    /**
     * 获取用户uid
     *
     * @param $uniacid
     * @param $mobile
     * @return mixed
     */
    public static function getId($uniacid, $mobile)
    {
        return self::select('uid')
            ->where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->first();
    }

    /**
     * 添加数据并返回id
     *
     * @param $data
     * @return mixed
     */
    public static function insertData($userinfo, $data)
    {
        $member_model = new MemberModel();

        $member_model->uniacid = $data['uniacid'];
        $member_model->email = '';
        $member_model->groupid = $data['groupid'];
        $member_model->createtime = time();
        $member_model->nickname = stripslashes($userinfo['nickname']);
        $member_model->avatar = $userinfo['headimgurl'];
        $member_model->gender = $userinfo['sex'];
        $member_model->nationality = $userinfo['country'] ?: '';
        $member_model->resideprovince = $userinfo['province'] . '省';
        $member_model->residecity = $userinfo['city'] . '市';
        $member_model->salt = '';
        $member_model->password = '';

        if ($member_model->save()) {
            return $member_model->uid;
        } else {
            return false;
        }
    }

    /**
     * 检查手机号是否存在
     *
     * @param $uniacid
     * @param $mobile
     * @return mixed
     */
    public static function checkMobile($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->first();
    }

    /**
     * 获取用户信息
     *
     * @param $uniacid
     * @param $mobile
     * @param $password
     * @return mixed
     */
    public static function getUserInfo($uniacid, $mobile, $password)
    {
        return self::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->where('password', $password);
    }

    /**
     * 更新数据
     *
     * @param $uid
     * @param $data
     * @return mixed
     */
    public static function updataData($uid, $data)
    {
        return self::uniacid()
            ->where('uid', $uid)
            ->update($data);
    }

    /**
     * 我的推荐人信息
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyReferrerInfo($uid)
    {
        return self::select(['uid'])->uniacid()
            ->where('uid', $uid)
            ->with([
                'yzMember' => function ($query) {
                    return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay', 'status', 'inviter'])
                        ->where('is_black', 0)
                        ->with(['level'=>function($query2){
                            return $query2->select(['id','level_name'])->uniacid();
                        }]);
                }
            ]);
    }

    /**
     * 获取我的下线
     *
     * @return mixed
     */
    public static function getAgentCount($uid)
    {
        return self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid){
                $query->where('parent_id', $uid)->where('inviter', 1);
            })
            ->count();
    }

    /**
     * 我的下线信息 1级
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyAgentInfo($uid)
    {
        return self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid){
                         $query->where('parent_id', $uid)->where('inviter', 1);
            })
            ->with(['hasOneOrder' => function ($query) {
                return $query->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }]);
    }

    public static function getMyAllAgentsInfo($uid, $level)
    {
        return self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid, $level){
                if (1 == $level) {
                    $query->where('parent_id', $uid)->where('inviter', 1);
                } else {
                    $query->where('inviter', 1)->whereRaw('FIND_IN_SET(?, relation)' . ($level != 0 ? ' = ?' : ''), [$uid, $level]);
                }
            })
            ->with(['yzMember' => function ($query) {
                return $query->select('member_id', 'is_agent', 'status');
            }, 'hasOneOrder' => function ($query) {
                return $query->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }]);
    }

    public static function getMyAllAgentsInfoBySearch($uid, $level, $keyword, $role_level)
    {
        $commission = self::langFiled('commission');
        $commission_filed = $commission['agent'] ?: '分销商';

        $result = self::uniacid()
            ->whereHas('yzMember', function($query) use ($uid, $level){
                if (1 == $level) {
                    $query->where('parent_id', $uid)->where('inviter', 1);
                } else {
                    $query->where('inviter', 1)->whereRaw('FIND_IN_SET(?, relation)' . ($level != 0 ? ' = ?' : ''), [$uid, $level]);
                }
            });

            if (!empty($keyword)) {
                switch ($keyword) {
                    case $commission_filed:
                        $result = $result->whereHas('hasOneAgent', function($query) use ($role_level) {
                            if (!empty($role_level)) {
                                $query->where('agent_level_id', $role_level);
                            }
                        });
                        break;
                    case '经销商':
                        $result = $result->whereHas('hasOneTeamDividend', function ($query) use ($role_level) {
                            if (!empty($role_level)) {
                                $query->where('level', $role_level);
                            }
                        });
                        break;
                    case '区域代理':
                        $result = $result->whereHas('hasOneAreaDividend', function ($query) use ($role_level) {
                            if (!empty($role_level)) {
                                $query->where('agent_level', $role_level);
                            }
                        });
                        break;
                    case '招商员':
                        $result = $result->whereHas('hasOneMerchant');
                        break;
                    case '招商中心':
                        $result = $result->whereHas('hasOneMerchantCenter', function ($query) use ($role_level) {
                            if (!empty($role_level)) {
                                $query->where('level_id', $role_level);
                            }
                        });
                        break;
                    case '微店店主':
                        $result = $result->whereHas('hasOneMicro', function ($query) use ($role_level) {
                            if (!empty($role_level)) {
                                $query->where('level_id', $role_level);
                            }
                        });
                        break;
                    case '供应商':
                        $result = $result->whereHas('hasOneSupplier');
                        break;
                }
            }

        $result =  $result->with(['yzMember' => function ($query) {
            return $query->select('member_id', 'is_agent', 'status');
        }, 'hasOneOrder' => function ($query) {
            return $query->selectRaw('uid, count(uid) as total, sum(price) as sum')
                ->uniacid()
                ->where('status', 3)
                ->groupBy('uid');
        }]);


        return $result;
    }

    /**
     * 我的下线信息 3级
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyAgentsInfo($uid)
    {
        return self::uniacid()
            ->with(['hasManyYzMember' => function ($query) {

                return $query->with(['hasManySelf' => function ($query) {

                    return $query->with(['hasManySelf' => function ($query) {

                        return $query->get();
                    }])->get();
                }])->get();
            }])
            ->where('uid', $uid);
    }

    /**
     * 我的上级 3级
     *
     * @param $uid
     * @return mixed
     */
    public static function getMyAgentsParentInfo($uid)
    {
        return self::select(['uid'])
            ->uniacid()
            ->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'parent_id'])
                    ->with(['hasOnePreSelf' => function ($query) {
                        return $query->select(['member_id', 'parent_id'])
                        ->with(['hasOnePreSelf' => function ($query) {
                            return $query->select(['member_id', 'parent_id'])
                            ->with(['hasOnePreSelf' => function ($query) {
                                return $query->select(['member_id', 'parent_id'])->first();
                        }])->first();
                    }])->first();
                }])->first();
            }])
            ->where('uid', $uid);
    }

    /**
     *
     * @return mixed
     */
    public function hasManyYzMember()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'parent_id', 'uid');
    }

    /**
     * 用户是否有推广权限
     *
     * @return mixed
     */
    public static function isAgent()
    {
        $uid = \YunShop::app()->getMemberId();

        if (!empty($uid)) {
            //MemberRelation::checkAgent($uid);

            $member_info = SubMemberModel::getMemberShopInfo($uid);

            if ($member_info && $member_info->is_agent == 1 && $member_info->status == 2) {
                return true;
            }
        }

        return false;

    }

    /**
     * 我的推荐人
     *
     * @return array
     */
    public static function getMyReferral()
    {
        $member_info = self::getMyReferrerInfo(\YunShop::app()->getMemberId())->first();

        $set = \Setting::get('shop.member');

        $data = [];

        if (!empty($member_info)) {
            if (isset($set) && $set['headimg']) {
                $avatar = replace_yunshop(tomedia($set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }

            $member_info = $member_info->toArray();

            $referrer_info = self::getUserInfos($member_info['yz_member']['parent_id'])->first();

            if ($member_info['yz_member']['inviter'] == 1) {
                if (!empty($referrer_info)) {
                    $info = $referrer_info->toArray();
                    $data = [
                        'uid' => $info['uid'],
                        'avatar' => $info['avatar'],
                        'nickname' => $info['nickname'],
                        'level' => $info['yz_member']['level']['level_name'],
                        'is_show' => $set['is_referrer']
                    ];
                } else {
                    $data = [
                        'uid' => '',
                        'avatar' => $avatar,
                        'nickname' => '总店',
                        'level' => '',
                        'is_show' => $set['is_referrer']
                    ];
                }
            } else {
                $data = [
                    'uid' => '',
                    'avatar' => $avatar,
                    'nickname' => '暂无',
                    'level' => '',
                    'is_show' => $set['is_referrer']
                ];
            }
        }

        return $data;
    }

    /**
     * 我的推荐人 v2
     *
     * @return array
     */
    public static function getMyReferral_v2()
    {
        $member_info     = self::getMyReferrerInfo(\YunShop::app()->getMemberId())->first();

        $set = \Setting::get('shop.member');
      
        $data = [];

        if (!empty($member_info)) {
            if (isset($set) && $set['headimg']) {
                $avatar = replace_yunshop(yz_tomedia($set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }

            $member_info = $member_info->toArray();

            $builder = self::getUserInfos($member_info['yz_member']['parent_id']);
            $referrer_info = self::getMemberRole($builder)->first();

            $member_role = self::convertRoleText($referrer_info);

            if ($member_info['yz_member']['inviter'] == 1) {
                if (!empty($referrer_info)) {
                    $info = $referrer_info->toArray();
                    $data = [
                        'uid' => $info['uid'],
                        'avatar' => $info['avatar'],
                        'nickname' => $info['nickname'],
                        'level' => $info['yz_member']['level']['level_name'],
                        'is_show' => $set['is_referrer'],
                        'role'   => $member_role
                    ];
                } else {
                    $data = [
                        'uid' => '',
                        'avatar' => $avatar,
                        'nickname' => '总店',
                        'level' => '',
                        'is_show' => $set['is_referrer'],
                        'role'   => $member_role
                    ];
                }
            } else {
                $data = [
                    'uid' => '',
                    'avatar' => $avatar,
                    'nickname' => '暂无',
                    'level' => '',
                    'is_show' => $set['is_referrer'],
                    'role'   => $member_role
                ];
            }
        }

        return $data;
    }

    /**
     * 推广二维码
     *
     * @param string $extra
     * @return mixed
     */
    public static function getAgentQR($extra='')
    {
        //把首页链接换成会员中心链接
        $url = Url::absoluteApp('/member');
        $url = $url . '&mid=' . \YunShop::app()->getMemberId();

        if (!empty($extra)) {
            $extra = '_' . $extra;
        }

        $extend = 'png';
        $filename = \YunShop::app()->uniacid . '_' . \YunShop::app()->getMemberId() . $extra . '.' . $extend;
        $path = storage_path('app/public/qr/');

        echo QrCode::format($extend)->size(400)->generate($url,  $path . $filename);

        return request()->getSchemeAndHttpHost() . '/' . substr($path, strpos($path, 'addons')) . $filename;
    }

    /**
     * 我推荐的人
     * @return array
     */
    public static function getMyAgent()
    {
        $agent_ids = [];
        $data = [];

        $agent_info = MemberModel::getMyAgentInfo(\YunShop::app()->getMemberId());
        $agent_model = $agent_info->get();

        if (!empty($agent_model)) {
            $agent_data = $agent_model->toArray();

            foreach ($agent_data as $key => $item) {
                $agent_ids[$key] = $item['uid'];
                $agent_data[$key]['agent_total'] = 0;
            }
        } else {
            return '数据为空';
        }

        $all_count = MemberShopInfo::getAgentAllCount($agent_ids);

        foreach ($all_count as $k => $rows) {
            foreach ($agent_data as $key => $item) {
                if ($rows['parent_id'] == $item['uid']) {
                    $agent_data[$key]['agent_total'] = $rows['total'];

                    break 1;
                }
            }
        }

        if ($agent_data) {
            foreach ($agent_data as $item) {
                $data[] = [
                    'uid' => $item['uid'],
                    'avatar' => $item['avatar'],
                    'nickname' => $item['nickname'],
                    'order_total' => $item['has_one_order']['total'],
                    'order_price' => $item['has_one_order']['sum'],
                    'agent_total' => $item['agent_total'],
                ];
            }
        }

        return $data;
    }

    /**
     * 我推荐的人v2 基本信息
     * @return array
     */
    public static function getMyAgent_v2()
    {
        set_time_limit(0);

        $data = [
            'total' => 0
        ];

        $total = 0;
        $relation_base = \Setting::get('relation_base');

        if (!is_null($relation_base['relation_level'])) {
            $agent_level = $relation_base['relation_level'];
        }

        for ($i = 1; $i <= 3; $i++) {
            $text  = 'level' . $i;

            switch ($i) {
                case 1:
                    $is_show = in_array($i, $agent_level) ?: false;
                    $level = '一级';

                    break;
                case 2:
                    $is_show = in_array($i, $agent_level) ?: false;
                    $level = '二级';

                    break;
                case 3:
                    $is_show = in_array($i, $agent_level) ?: false;
                    $level = '三级';

                    break;
            }

            if ($is_show) {
                $builder = MemberModel::getMyAllAgentsInfo(\YunShop::app()->getMemberId(), $i);
                $agent_info = self::getMemberRole($builder)->get();

                $agent_data = $agent_info->toArray();

                $total += count($agent_data);

                $data[$text] = [
                    'level' => $level,
                    'total' => count($agent_data),
                    'is_show' => $is_show
                ];
            } else {
                $total += 0;

                $data[$text] = [
                    'level' => $level,
                    'total' => 0,
                    'is_show' => $is_show
                ];
            }
        }

        $data['total'] = $total;

        return $data;
    }

    /**
     * 我推荐的人v2 数据
     * @return array
     */
    public static function getMyAgentData_v2()
    {
        set_time_limit(0);

        $pageSize = 10;
        $data = [];
        $keyword = \YunShop::request()->keyword ?: '';
        $level   = \YunShop::request()->level ?: 0;

        $i = \YunShop::request()->relationLevel ?: 0;

        $builder = MemberModel::getMyAllAgentsInfoBySearch(\YunShop::app()->getMemberId(), $i, $keyword, $level);

        $agent_info = self::getMemberRole($builder)->paginate($pageSize);

        $agent_data = self::fetchAgentInfo($agent_info->items());

        if (!empty($agent_data)) {
            $data = $agent_data->toArray();

            $data = [
                'current_page' => $agent_info->currentPage() ?: 1,
                'last_page' => $agent_info->lastPage() ?: 1,
                'data' => $data
            ];
        }

       /* if (empty($keyword)) {
            return $data;
        }

        $data = self::searchMemberRelation($data);*/

        return $data;
    }

    /**
     * 会员中心返回数据
     *
     * @param $member_info
     * @param $yz_member
     * @return mixed
     */
    public static function userData($member_info, $yz_member)
    {
        if (!empty($yz_member)) {
            $member_info['alipay_name'] = $yz_member['alipayname'];
            $member_info['alipay'] =  $yz_member['alipay'];
            $member_info['province_name'] =  $yz_member['province_name'];
            $member_info['city_name'] =  $yz_member['city_name'];
            $member_info['area_name'] =  $yz_member['area_name'];
            $member_info['province'] =  $yz_member['province'];
            $member_info['city'] =  $yz_member['city'];
            $member_info['area'] =  $yz_member['area'];
            $member_info['address'] =  $yz_member['address'];
            $member_info['wechat'] =  $yz_member['wechat'];

            if (!empty( $yz_member['group'])) {
                $member_info['group_id'] =  $yz_member['group']['id'];
                $member_info['group_name'] =  $yz_member['group']['group_name'];
            }

            if (!empty( $yz_member['level'])) {
                $member_info['level_id'] =  $yz_member['level']['id'];
                $member_info['level_name'] =  $yz_member['level']['level_name'];
            } else {
                $set = \Setting::get('shop.member');
                $member_info['level_id'] =  0;
                $member_info['level_name'] =  $set['level_name'] ? $set['level_name'] : '普通会员';
            }
        }

        if (!empty($member_info['birthyear'] )) {
            $member_info['birthday'] = date('Y-m-d', strtotime($member_info['birthyear'] . '-'. $member_info['birthmonth'] . '-' .$member_info['birthday']));
        } else {
            $member_info['birthday'] = date('Y-m-d', time());
        }

        $order_info = \app\frontend\models\Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE,Order::REFUND]);

        $member_info['order'] = $order_info;

        $member_info['is_agent'] = self::isAgent();
        $member_info['referral'] = self::getMyReferral();

        self::createDir(storage_path('app/public/qr'));
        self::createDir(storage_path('app/public/avatar'));

        $member_info['qr'] = self::getAgentQR();
        $member_info['avatar_dir'] =  request()->getSchemeAndHttpHost() . '/addons/yun_shop/storage/app/public/avatar/';

        $shop = \Setting::get('shop.shop');
        $member_info['copyright'] = $shop['copyright'] ? $shop['copyright'] : '';
        $member_info['credit'] = [
            'text' => !empty($shop['credit']) ? $shop['credit'] : '余额',
            'data' => $member_info['credit2']
            ];
        $member_info['integral'] = [
            'text' => !empty($shop['credit1']) ? $shop['credit1'] : '积分',
            'data' => $member_info['credit1']
            ];


        return $member_info;
    }

    function createDir($dest)
    {
        if (!is_dir($dest)) {
            (@mkdir($dest, 0777, true));
        }
    }

    public static function convertRoleText($member_modle)
    {
         $commission = self::langFiled('commission');

         $member_role = '';

         if (!is_null($member_modle)) {
             if (app('plugins')->isEnabled('commission')) {
                 if (!is_null($member_modle->hasOneAgent)) {
                     $member_role .= $commission['agent'] ?:'分销商';
                     $member_role .= '&';
                 }
             }

             if (app('plugins')->isEnabled('team-dividend')) {
                 if (!is_null($member_modle->hasOneTeamDividend)) {
                     $member_role .= '经销商&';
                 }
             }

             if (app('plugins')->isEnabled('area-dividend')) {
                 if (!is_null($member_modle->hasOneAreaDividend)) {
                     $member_role .= '区域代理&';
                 }
             }

             if (app('plugins')->isEnabled('merchant')) {
                 if (!is_null($member_modle->hasOneMerchant)) {
                     $member_role .= '招商员&';
                 }

                 if (!is_null($member_modle->hasOneMerchantCenter)) {
                     if (1 == $member_modle->hasOneMerchant->is_center) {
                         $member_role .= '招商中心&';
                     }
                 }
             }

             if (app('plugins')->isEnabled('micro')) {
                 if (!is_null($member_modle->hasOneMicro)) {
                     $member_role .= '微店店主&';
                 }
             }

             if (app('plugins')->isEnabled('supplier')) {
                 if (!is_null($member_modle->hasOneSupplier)) {
                     $member_role .= '供应商&';
                 }
             }
         }

         if (!empty($member_role)) {
             $member_role = rtrim($member_role, '&');
         }

         return $member_role;
    }

    public static function fetchAgentInfo($agent_info)
    {
        if (empty($agent_info)) {
            return [];
        }

        return collect($agent_info)->map(function($item) {
            $is_agent          = 0;
            $order_price       = 0;
            $agent_total       = 0;
            $agent_order_price = 0;

            $role              = self::convertRoleText($item);
            $role_type         = self::setRoleLevel($item);

            //下线的下线1级
            $child_agent = MemberModel::getMyAllAgentsInfo($item->uid, 1)->get();

            if (!is_null($child_agent)) {
                $agent_total = count($child_agent);

                foreach ($child_agent as $rows) {
                    $agent_order_price += $rows->hasOneOrder->sum;
                }
            }

            if (!is_null($item->yzMember)) {
                if (1 == $item->yzMember->is_agent && 2 == $item->yzMember->status) {
                    $is_agent = 1;
                }
            }

            if (!is_null($item->hasOneOrder)) {
                $order_price = $item->hasOneOrder->sum;
            }

            return [
                'id' => $item->uid,
                'is_agent' => $is_agent,
                'nickname' => $item->nickname,
                'avatar'   => $item->avatar,
                'order_price' => $order_price,
                'agent_total' => $agent_total,
                'agent_order_price' => $agent_order_price,
                'role' => $role,
                'role_type' => $role_type
            ];
        });
    }

    public static function searchMemberRelation($data)
    {
        $result = [];
        $keyword = \YunShop::request()->keyword;
        $level   = \YunShop::request()->level;
        $filter  = ['招商员', '供应商']; //没有等级

        $coll = collect($data)->filter(function ($item) use ($keyword, $level, $filter) {
            $role_level = false;
            $role       = preg_match("/{$keyword}/", $item['role']);

            if ($role) {
                if (in_array($keyword, $filter) || empty($level)) {
                    $role_level = true;
                }

                if (!empty($item['role_type'])) {
                    foreach ($item['role_type'] as $rows) {
                        foreach ($rows as $key => $val) {
                            if ($key == $keyword && $val == $level) {
                                $role_level = true;
                            }

                            break 2;
                        }
                    }
                }
            }

            return $role && $role_level;
        });

        if (!$coll->isEmpty()) {
            $result = array_values($coll->toArray());
        }

        return $result;
    }

    public static function setRoleLevel($member_modle)
    {
        $commission = self::langFiled('commission');
        $commission_filed = $commission['agent'] ?: '分销商';
        $role_type = [];

        if (!is_null($member_modle)) {
            if (app('plugins')->isEnabled('commission')) {
                if (!is_null($member_modle->hasOneAgent)) {
                    array_push($role_type, [$commission_filed=>$member_modle->hasOneAgent->agent_level_id]);
                }
            }

            if (app('plugins')->isEnabled('team-dividend')) {
                if (!is_null($member_modle->hasOneTeamDividend)) {
                    array_push($role_type, ['经销商'=>$member_modle->hasOneTeamDividend->level]);
                }
            }

            if (app('plugins')->isEnabled('area-dividend')) {
                if (!is_null($member_modle->hasOneAreaDividend)) {
                    array_push($role_type, ['区域代理'=>$member_modle->hasOneAreaDividend->agent_level]);
                }
            }

            if (app('plugins')->isEnabled('merchant')) {
                if (!is_null($member_modle->hasOneMerchant)) {
                }

                if (!is_null($member_modle->hasOneMerchantCenter)) {
                    if (1 == $member_modle->hasOneMerchant->is_center) {
                        array_push($role_type, ['招商中心'=>$member_modle->hasOneMerchantCenter->level_id]);
                    }
                }
            }

            if (app('plugins')->isEnabled('micro')) {
                if (!is_null($member_modle->hasOneMicro)) {
                    array_push($role_type, ['微店店主'=>$member_modle->hasOneMicro->level_id]);
                }
            }

            if (app('plugins')->isEnabled('supplier')) {
                if (!is_null($member_modle->hasOneSupplier)) {
                }
            }
        }

        return $role_type;
    }

    public static function filterMemberRoleAndLevel()
    {
        $commission = self::langFiled('commission');
        $commission_filed = $commission['agent'] ?: '分销商';
        $data = [];

        if (app('plugins')->isEnabled('commission')) {
            $agent_level = AgentLevel::uniacid()->get();

            if (!$agent_level->isEmpty()) {
                $agent_level = collect($agent_level)->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'level_name' => $item->name
                    ];
                });

                array_push($data, ['role' => $commission_filed, 'level' =>$agent_level->all()]);
            } else {
                array_push($data, ['role' => $commission_filed, 'level' =>[]]);
            }
        }

        if (app('plugins')->isEnabled('team-dividend')) {
            $teamdividend_level = TeamDividendLevelModel::uniacid()->get();

            if (!$teamdividend_level->isEmpty()) {
                array_push($data, ['role' => '经销商', 'level' =>$teamdividend_level->toArray()]);
            } else {
                array_push($data, ['role' => '经销商', 'level' =>[]]);
            }
        }

        if (app('plugins')->isEnabled('area-dividend')) {
            array_push($data, ['role' => '区域代理', 'level' =>[
                ['id' =>1, 'level_name'=>'省代理'],['id' =>2, 'level_name'=>'市代理'],['id' =>3, 'level_name'=>'区代理'],['id'=>4, 'level_name'=>'街道代理']
            ]]);
        }

        if (app('plugins')->isEnabled('merchant')) {
            array_push($data, ['role' => '招商员', 'level' =>[]]);

            $merchant_level = MerchantLevel::uniacid()->get();

            if (!$merchant_level->isEmpty()) {
                array_push($data, ['role' => '招商中心', 'level' =>$merchant_level->toArray()]);
            } else {
                array_push($data, ['role' => '招商中心', 'level' =>[]]);
            }
        }

        if (app('plugins')->isEnabled('micro')) {
            $microShop_level = MicroShopLevel::uniacid()->get();
            if (!$microShop_level->isEmpty()) {
                array_push($data, ['role' => '微店店主', 'level' =>$microShop_level->toArray()]);
            } else {
                array_push($data, ['role' => '微店店主', 'level' =>[]]);
            }
        }

        if (app('plugins')->isEnabled('supplier')) {
            array_push($data, ['role' => '供应商', 'level' =>[]]);
        }

        return $data;
    }

    private static function langFiled($filed)
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);
        $set = $lang[$lang['lang']];

        return $set[$filed];
    }
}