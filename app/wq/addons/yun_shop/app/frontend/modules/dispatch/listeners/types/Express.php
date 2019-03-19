<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 上午10:40
 */

namespace app\frontend\modules\dispatch\listeners\types;

use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\events\order\AfterOrderCreatedEvent;

use app\common\events\order\OnPreGenerateOrderCreatingEvent;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\models\Address;
use app\common\models\OrderAddress;
use app\common\models\Street;
use app\frontend\modules\member\models\MemberAddress;
use app\frontend\repositories\MemberAddressRepository;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Express
{
    use ValidatesRequests;
    private $event;

    /**
     * 订单模型刚生成时(未添加订单商品)
     * @param OnPreGenerateOrderCreatingEvent $event
     */
    public function onCreating(OnPreGenerateOrderCreatingEvent $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        // 添加订单收货地址管理模型,当订单商品计算区域运费时需要使用这个模型
        $event->getOrderModel()->setRelation('orderAddress', $this->getOrderAddress());
    }

    /**
     * 订单保存时
     * @param AfterOrderCreatedEvent $event
     */
    public function onSave(AfterOrderCreatedEvent $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        //保存信息
        $this->saveExpressInfo();

        return;
    }

    /**
     * 显示配送方式
     * @param OnDispatchTypeInfoDisplayEvent $event
     */
    public function onDisplay(OnDispatchTypeInfoDisplayEvent $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        //获取用户当前默认地址

        $data = $this->getMemberAddress();
        if (!isset($data)) {
            $event->addMap('default_member_address', new \ArrayObject());
            return;
        }

        $event->addMap('default_member_address', $data);
        return;
    }

    /**
     * 获取用户配送地址模型
     * @return MemberAddress
     */
    private function getMemberAddress()
    {
        $request = \Request::capture();
        $address = json_decode($request->input('address', '[]'), true);

        if (count($address)) {
            //$request->input('address');
            $this->validate([
                'address.address' => 'required|string',
                'address.mobile' => 'required|string',
                'address.username' => 'required|string',
                'address.province' => 'required|string',
                'address.city' => 'required|string',
                'address.district' => 'required|string',
            ], ['address' => $address]
            );
            $memberAddress = app(MemberAddressRepository::class)->fill($address);

            return $memberAddress;
        }

        return $this->event->getOrderModel()->getMember()->defaultAddress;
    }

    /**
     * 需要配送
     * @return bool
     */
    private function needDispatch()
    {
        return $this->event->getOrderModel()->needSend();
    }

    /**
     * 获取订单配送地址模型
     * @return OrderAddress
     */
    private function getOrderAddress()
    {
        $member_address = $this->getMemberAddress();

        $orderAddress = new OrderAddress();

        $orderAddress->order_id = $this->event->getOrderModel()->id;
        $orderAddress->mobile = $member_address->mobile;
        $orderAddress->province_id = Address::where('areaname', $member_address->province)->value('id');
        $orderAddress->city_id = Address::where('areaname', $member_address->city)->where('parentid', $orderAddress->province_id)->value('id');
        $orderAddress->district_id = Address::where('areaname', $member_address->district)->where('parentid', $orderAddress->city_id)->value('id');
        $orderAddress->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $member_address->address]);

        if (isset($member_address->street)) {
            $orderAddress->street_id = Street::where('areaname', $member_address->street)->where('parentid', $orderAddress->district_id)->value('id');
            if(!isset($orderAddress->street_id)){
                throw new ShopException('收货地址有误请重新保存收货地址');
            }
            $orderAddress->street = $member_address->street;
            $orderAddress->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $orderAddress->street, $member_address->address]);

        }

        $orderAddress->realname = $member_address->username;
        $orderAddress->province = $member_address->province;
        $orderAddress->city = $member_address->city;
        $orderAddress->district = $member_address->district;

        return $orderAddress;
    }

    /**
     * 保存配送信息
     * @return bool
     * @throws AppException
     */
    private function saveExpressInfo()
    {
        $orderAddress = $this->getOrderAddress();

        if (!$orderAddress->save()) {
            throw new AppException('订单地址保存失败');
        }
        return true;
    }

    public function subscribe($events)
    {
        $events->listen(
            OnDispatchTypeInfoDisplayEvent::class,
            Express::class . '@onDisplay'
        );
        $events->listen(
            AfterOrderCreatedEvent::class,
            Express::class . '@onSave'
        );
        $events->listen(
            OnPreGenerateOrderCreatingEvent::class,
            Express::class . '@onCreating'
        );

    }

    /**
     * 校验参数
     * @param $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @throws AppException
     */
    private function validate($request, array $rules, array $messages = [], array $customAttributes = [])
    {

        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);
        //$validator->errors();
        if ($validator->fails()) {
            throw new AppException($validator->errors()->first());
        }
    }
}