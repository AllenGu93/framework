<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:34
 */
namespace Notadd\Auth;
use Illuminate\Auth\Access\Gate;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\ServiceProvider;
use Notadd\Auth\Social\SocialManager;
use Notadd\Foundation\Traits\InjectRouterTrait;
use Notadd\Foundation\Traits\InjectSettingTrait;
/**
 * Class AuthServiceProvider
 * @package Notadd\Auth
 */
class AuthServiceProvider extends ServiceProvider {
    use InjectRouterTrait, InjectSettingTrait;
    /**
     * @return void
     */
    public function boot() {
        $this->getRouter()->group([
            'namespace' => 'Notadd\Auth\Controllers\Admin',
            'prefix' => 'admin'
        ], function () {
            $this->getRouter()->get('third', 'ConfigController@getThird');
            $this->getRouter()->post('third', 'ConfigController@postThird');
        });
    }
    /**
     * @return void
     */
    public function register() {
        $this->registerAuthenticator();
        $this->registerUserResolver();
        $this->registerAccessGate();
        $this->registerRequestRebindHandler();
        $this->app->singleton(SocialManager::class, function () {
            $config = [
                'qq' => [
                    'client_id' => $this->getSetting()->get('third.qq.key'),
                    'client_secret' => $this->getSetting()->get('third.qq.secret'),
                    'redirect' => $this->getSetting()->get('third.qq.callback'),
                ],
                'weibo' => [
                    'client_id' => $this->getSetting()->get('third.weibo.key'),
                    'client_secret' => $this->getSetting()->get('third.weibo.secret'),
                    'redirect' => $this->getSetting()->get('third.weibo.callback'),
                ],
                'weixin' => [
                    'client_id' => $this->getSetting()->get('third.weixin.key'),
                    'client_secret' => $this->getSetting()->get('third.weixin.secret'),
                    'redirect' => $this->getSetting()->get('third.weixin.callback'),
                ],
            ];
            return new SocialManager($config);
        });
    }
    /**
     * @return void
     */
    protected function registerAuthenticator() {
        $this->app->singleton('auth', function ($app) {
            $app['auth.loaded'] = true;
            return new AuthManager($app);
        });
        $this->app->singleton('auth.driver', function ($app) {
            return $app['auth']->driver();
        });
    }
    /**
     * @return void
     */
    protected function registerUserResolver() {
        $this->app->bind(AuthenticatableContract::class, function ($app) {
            return $app['auth']->user();
        });
    }
    /**
     * @return void
     */
    protected function registerAccessGate() {
        $this->app->singleton(GateContract::class, function ($app) {
            return new Gate($app, function () use ($app) {
                return $app['auth']->user();
            });
        });
    }
    /**
     * @return void
     */
    protected function registerRequestRebindHandler() {
        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                return $app['auth']->user();
            });
        });
    }
}