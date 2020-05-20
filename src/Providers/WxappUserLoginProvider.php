<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2019-12-03
 * Time: 18:08
 */

namespace JoseChan\UserLogin\Wechat\MiniProgram\Providers;


use Illuminate\Support\ServiceProvider;
use JoseChan\UserLogin\Handler\Login;
use JoseChan\UserLogin\Wechat\MiniProgram\LoginExtensions\WxappLogin;

class WxappUserLoginProvider extends ServiceProvider
{
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Login::extend("wechat_mini_program", WxappLogin::class);
    }


}