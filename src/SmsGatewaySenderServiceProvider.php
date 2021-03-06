<?php
/**
 * @author: Michael Kumar <michael.kumar@sirma.bg>
 * Date: 15.08.17
 * Time: 13:32
 */

namespace xXc\SmsGatewaySender;

use Illuminate\Support\ServiceProvider;

class SmsGatewaySenderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
    }

    public function register()
    {
        if(file_exists(__DIR__.'/../config/smsgateway_sender.php'))
        {
            $this->mergeConfigFrom(__DIR__.'/../config/smsgateway_sender.php', 'SmsGatewaySender');
        }
    }

    private function publishConfig()
    {
        $path = $this->getConfigPath();
        $this->publishes([$path => config_path('smsgateway_sender.php')], 'config');
    }


    private function getConfigPath()
    {
        return __DIR__ . '/../config/smsgateway_sender.php';
    }
}