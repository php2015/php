<?php

namespace app\common\providers;

use app\common\models\AccountWechats;
use app\common\services\Check;
use app\common\services\Session;
use Illuminate\Database\Eloquent\Relations\Relation;
use Setting;
use Illuminate\Support\ServiceProvider;
use App;
use Illuminate\Support\Facades\DB;
use app\common\repositories\OptionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        \Cron::setDisablePreventOverlapping();

        //微信接口不输出错误
        if (strpos(request()->getRequestUri(), '/api.php') >= 0) {
            error_reporting(0);
            //strpos(request()->get('route'),'setting.key') !== 0 && Check::app();
        }

        //设置uniacid
        Setting::$uniqueAccountId = \YunShop::app()->uniacid;
        //设置公众号信息
        AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));

        //开发模式下记录SQL
        if ($this->app->environment() !== 'production') {
            DB::listen(
                function ($sql) {
                    // $sql is an object with the properties:
                    //  sql: The query
                    //  bindings: the sql query variables
                    //  time: The execution time for the query
                    //  connectionName: The name of the connection

                    // To save  the executed queries to file:
                    // Process the sql and the bindings:
                    foreach ($sql->bindings as $i => $binding) {
                        if ($binding instanceof \DateTime) {
                            $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                        } else {
                            if (is_string($binding)) {
                                $sql->bindings[$i] = "'$binding'";
                            }
                        }
                    }

                    // Insert bindings into query
                    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

                    $query = vsprintf($query, $sql->bindings);

                    // Save the query to file
                    $logFile = fopen(
                        storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                        'a+'
                    );
                    //echo storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log');exit;
                    fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
                    fclose($logFile);
                }
            );
        }


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Orangehill\Iseed\IseedServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }

        //增加模板扩展tpl
        \View::addExtension('tpl', 'blade');
        //配置表
        $this->app->singleton('options',  OptionRepository::class);

        /**
         * 设置
         */
        App::bind('setting', function()
        {
            return new Setting();
        });

        App::bind(\Illuminate\Session\Store::class,function(){
            return new Session();
        });

    }
}
