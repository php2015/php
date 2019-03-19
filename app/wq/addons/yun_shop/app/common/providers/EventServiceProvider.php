<?php

namespace app\common\providers;


use app\common\events\message\SendMessageEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\PayLog;
use app\common\events\WechatProcessor;
use app\common\listeners\PayLogListener;
use app\common\listeners\point\PointListener;
use app\common\listeners\WechatProcessorListener;
use app\frontend\modules\coupon\listeners\CouponSend;
use app\frontend\modules\finance\listeners\IncomeWithdraw;
use app\frontend\modules\goods\listeners\GoodsStock;
use app\frontend\modules\member\listeners\MemberLevelValidity;
use app\frontend\modules\order\listeners\orderListener;
use app\frontend\modules\coupon\listeners\CouponExpireNotice;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use app\backend\modules\goods\listeners\LimitBuy;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \app\common\events\dispatch\OrderDispatchWasCalculated::class => [
            //订单邮费计算
            \app\frontend\modules\dispatch\listeners\prices\UnifyOrderDispatchPrice::class, //统一运费
            \app\frontend\modules\dispatch\listeners\prices\TemplateOrderDispatchPrice::class, //模板运费

        ],

        PayLog::class => [ //支付日志请求
            PayLogListener::class //保存支付参数
        ],
        \app\common\events\member\BecomeAgent::class => [ //会员成为下线
            \app\common\listeners\member\BecomeAgentListener::class
        ],
        AfterOrderCreatedEvent::class => [ //下单成功后调用会员成为下线事件
            \app\common\listeners\member\AfterOrderCreatedListener::class,
            \app\frontend\modules\member\listeners\Order::class, //清空购物车
        ],
        AfterOrderReceivedEvent::class => [ //确认收货
            \app\common\listeners\member\AfterOrderReceivedListener::class
        ],
        AfterOrderPaidEvent::class => [ //支付完成
            \app\common\listeners\member\AfterOrderPaidListener::class
        ],
        //微信接口回调触发事件进程
        WechatProcessor::class => [
            WechatProcessorListener::class//示例监听类
        ],

        //发送消息触发事件
        SendMessageEvent::class => [

        ],
    ];
    /**
     * 注册监听着类
     * @var array
     */
    protected $subscribe = [
        \app\common\listeners\MessageListener::class,

        //会员等级升级
        \app\common\listeners\member\level\LevelListener::class,
        \app\common\listeners\balance\BalanceListener::class,


        \app\frontend\modules\dispatch\listeners\types\Express::class,
        \app\frontend\modules\coupon\listeners\CouponDiscount::class,
        PointListener::class,
        GoodsStock::class,
        //Order::class,
        \app\frontend\modules\discount\listeners\Order::class,
        \app\frontend\modules\payment\listeners\Alipay::class,
        \app\frontend\modules\payment\listeners\Credit::class,
        \app\frontend\modules\payment\listeners\Wechat::class,
        \app\frontend\modules\payment\listeners\CloudPay::class,
        \app\frontend\modules\payment\listeners\Wechat_App::class,
        \app\frontend\modules\payment\listeners\Alipay_App::class,
        \app\frontend\modules\payment\listeners\YunPay::class,
        \app\frontend\modules\payment\listeners\Cloud_Alipay::class,
        \app\frontend\modules\payment\listeners\Yun_Alipay::class,
        orderListener::class,
        IncomeWithdraw::class,
        CouponExpireNotice::class,
        CouponSend::class,
        MemberLevelValidity::class,
        LimitBuy::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
