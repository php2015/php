<?php

namespace app\frontend\modules\order\listeners;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderSentEvent;
use app\common\models\Order;
use app\common\models\UniAccount;
use app\frontend\modules\order\services\MessageService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\order\services\OtherMessageService;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/5
 * Time: 下午8:53
 */
class orderListener
{
    public function onCreated(AfterOrderCreatedEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        (new MessageService($order))->created();

        (new OtherMessageService($order))->created();
    }

    public function onPaid(AfterOrderPaidEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        if (!$order->isVirtual()) {
            (new MessageService($order))->paid();
            (new OtherMessageService($order))->paid();
        }
    }

    public function onCanceled(AfterOrderCanceledEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        (new MessageService($order))->canceled();
    }

    public function onSent(AfterOrderSentEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        if (!$order->isVirtual()) {
            (new MessageService($order))->sent();
            (new OtherMessageService($order))->sent();
        }
    }

    public function onReceived(AfterOrderReceivedEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        (new MessageService($order))->received();
        (new OtherMessageService($order))->received();
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCreatedEvent::class, self::class . '@onCreated');
        $events->listen(AfterOrderPaidEvent::class, self::class . '@onPaid');
        $events->listen(AfterOrderCanceledEvent::class, self::class . '@onCanceled');
        $events->listen(AfterOrderSentEvent::class, self::class . '@onSent');
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@onReceived');

        // 订单自动任务
        $events->listen('cron.collectJobs', function () {
            $uniAccount = UniAccount::get();
            foreach ($uniAccount as $u) {
                \YunShop::app()->uniacid = $u->uniacid;
                \Setting::$uniqueAccountId = $uniacid= $u->uniacid;

                // 订单自动收货执行间隔时间 默认60分钟
                $receive_min = (int)\Setting::get('shop.trade.receive_time') ?: 60;

                if ((int)\Setting::get('shop.trade.receive')) {
                    // 开启自动收货时
                    \Log::info("--订单自动完成start--");
                    \Cron::add("OrderReceive{$u->uniacid}", '*/' . $receive_min . ' * * * * *', function () use($uniacid) {
                        // 所有超时未收货的订单,遍历执行收货
                        OrderService::autoReceive($uniacid);
                        // todo 使用队列执行
                    });
                }

                // 订单自动关闭执行间隔时间 默认60分钟
                $close_min = (int)\Setting::get('shop.trade.close_order_time') ?: 60;


                if ((int)\Setting::get('shop.trade.close_order_days')) {
                    // 开启自动关闭时
                    \Log::info("--订单自动关闭start--");
                    \Cron::add("OrderClose{$u->uniacid}", '*/' . $close_min . ' * * * * *', function () use($uniacid) {
                        // 所有超时付款的订单,遍历执行关闭
                        OrderService::autoClose($uniacid);
                        // todo 使用队列执行
                    });
                }
            }
        });
    }
}