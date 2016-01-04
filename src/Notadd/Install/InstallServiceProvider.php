<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Install;
use Illuminate\Support\ServiceProvider;
use Notadd\Foundation\Traits\InjectRouterTrait;
use Notadd\Install\Console\InstallCommand;
use Notadd\Install\Contracts\Prerequisite;
use Notadd\Install\Prerequisites\Composite;
use Notadd\Install\Prerequisites\PhpExtensions;
use Notadd\Install\Prerequisites\PhpVersion;
use Notadd\Install\Prerequisites\WritablePaths;
/**
 * Class InstallServiceProvider
 * @package Notadd\Install
 */
class InstallServiceProvider extends ServiceProvider {
    use InjectRouterTrait;
    /**
     * @return void
     */
    public function boot() {
        $this->loadViewsFrom(realpath(__DIR__ . '/../../../views/install'), 'install');
        $this->getRouter()->get('/', 'Notadd\Install\Controllers\PrerequisiteController@render');
        $this->getRouter()->post('/', 'Notadd\Install\Controllers\InstallController@handle');
        $this->getRouter()->get('make', 'Notadd\Install\Controllers\InstallController@make');
    }
    /**
     * @return void
     */
    public function register() {
        $this->app->bind(Prerequisite::class, function () {
            return new Composite(new PhpVersion(), new PhpExtensions(), new WritablePaths());
        });
        $this->app->singleton('command.install', function($app) {
            return new InstallCommand($app, $app['files']);
        });
        $this->commands('command.install');
    }
}