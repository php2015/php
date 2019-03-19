<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\DispatchType;
use app\common\models\Order;
use app\common\requests\Request;
use app\frontend\models\OrderAddress;
use Yunshop\StoreCashier\common\models\StoreDelivery;
use app\common\services\plugin\leasetoy\LeaseToySet;
use app\common\services\goods\VideoDemandCourseGoods;

class DetailController extends ApiController
{
    public function index(Request $request)
    {
        $this->validate([
            'order_id' => 'required|integer'
        ]);
        $orderId = $request->query('order_id');

        $order = $this->getOrder()->with(['hasManyOrderGoods','orderDeduction','orderDiscount','orderCoupon'])->find($orderId);

//        if ($order->uid != \YunShop::app()->getMemberId()) {
//            throw new AppException('(ID:' . $order->id . ')该订单属于其他用户');
//        }

        if (is_null($order)) {
            throw new AppException('该订单查询失败');
        }

        $data = $order->toArray();
        $backups_button = $data['button_models'];
        $data['button_models'] = array_merge($data['button_models'],$order->getStatusService()->getRefundButtons($order));
        //$this->getStatusService()->
        //todo 配送类型
        if ($order['dispatch_type_id'] == DispatchType::EXPRESS) {
            $data['address_info'] = OrderAddress::select('address', 'mobile', 'realname')->where('order_id', $order['id'])->first();
        }
        if(app('plugins')->isEnabled('store-cashier')){

            //加入门店ID，订单跳转商品详情需要
            $store_id = \Yunshop\StoreCashier\store\models\StoreGoods::select()->byGoodsId($order->hasManyOrderGoods[0]->goods_id)->first()->store_id;
            $data['has_many_order_goods']['0']['store_id'] = $store_id;

            //临时解决
            $storeObj = \Yunshop\StoreCashier\common\models\Store::getStoreByCashierId($order->hasManyOrderGoods[0]->goods_id)->first();
            if ($storeObj) {
                $data['button_models'] = $backups_button;
            }

            if ($order['dispatch_type_id'] == DispatchType::SELF_DELIVERY) {
                $data['address_info'] = \Yunshop\StoreCashier\common\models\SelfDelivery::where('order_id', $order['id'])->first();
            }elseif($order['dispatch_type_id'] == DispatchType::STORE_DELIVERY){
                $data['address_info'] = \Yunshop\StoreCashier\common\models\StoreDelivery::where('order_id', $order['id'])->first();
            }
        }


        //租赁插件
        $lease_enabled = LeaseToySet::whetherEnabled();
        if ($lease_enabled && $order->plugin_id == 40) {
            $lease_toy = \Yunshop\LeaseToy\services\LeaseOrderDetail::detailInfo($order);
            foreach ($data['has_many_order_goods'] as &$goods) {
                $goods['lease_toy_goods'] = \Yunshop\LeaseToy\services\LeaseOrderDetail::LeaseOrderGoodsDetail($goods['id']);
            }

            if ($order->status > 2) {
                $data['button_models'] = array_merge($backups_button, $lease_toy['button']);
            } elseif ($order->status == 2) {
                $data['button_models'] = $backups_button;
            }
            $data['lease_toy'] = $lease_toy['data'];

        }
        //todo 临时解决
        if (!$order) {
            return $this->errorJson($msg = '未找到数据', []);
        } else {

            //视频点播

            $videoDemand = new VideoDemandCourseGoods();
            foreach ($data['has_many_order_goods'] as &$value) {
                $value['is_course'] = $videoDemand->isCourse($value['goods_id']);
            }

            return $this->successJson($msg = 'ok', $data);
        }

    }

    protected function getOrder()
    {
        return app('OrderManager')->make('Order');
    }
}