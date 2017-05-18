<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-10-21 11:56
 */
namespace Notadd\Foundation\Console;

use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Console\Migrations\ResetCommand as MigrateResetCommand;
use Illuminate\Database\Console\Migrations\StatusCommand as MigrateStatusCommand;
use Illuminate\Database\Console\Migrations\InstallCommand as MigrateInstallCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand as MigrateRefreshCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand as MigrateRollbackCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\TableCommand;
use Notadd\Foundation\Cache\Commands\CacheTableCommand;
use Notadd\Foundation\Console\Commands\AppNameCommand;
use Notadd\Foundation\Console\Commands\ClearCompiledCommand;
use Notadd\Foundation\Console\Commands\ConfigCacheCommand;
use Notadd\Foundation\Console\Commands\ConfigClearCommand;
use Notadd\Foundation\Console\Commands\DownCommand;
use Notadd\Foundation\Console\Commands\EnvironmentCommand;
use Notadd\Foundation\Console\Commands\EventGenerateCommand;
use Notadd\Foundation\Console\Commands\TestMakeCommand;
use Notadd\Foundation\Console\Commands\VendorPublishCommand;
use Notadd\Foundation\Database\Commands\SeederMakeCommand;
use Notadd\Foundation\Http\Abstracts\ServiceProvider;
use Notadd\Foundation\Mail\Commands\MailMakeCommand;
use Notadd\Foundation\Queue\Commands\JobMakeCommand;
use Notadd\Foundation\Routing\Commands\RouteCacheCommand;
use Notadd\Foundation\Routing\Commands\RouteClearCommand;
use Notadd\Foundation\Routing\Commands\RouteListCommand;
use Notadd\Foundation\Session\Commands\SessionTableCommand;

/**
 * Class ArtisanServiceProvider.
 */
class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var array
     */
    protected $commands = [
        'ClearCompiled'   => 'command.clear-compiled',
        'ClearResets'     => 'command.auth.resets.clear',
        'ConfigCache'     => 'command.config.cache',
        'ConfigClear'     => 'command.config.clear',
        'Down'            => 'command.down',
        'Environment'     => 'command.environment',
        'KeyGenerate'     => 'command.key.generate',
        'Migrate'         => 'command.migrate',
        'MigrateInstall'  => 'command.migrate.install',
        'MigrateRefresh'  => 'command.migrate.refresh',
        'MigrateReset'    => 'command.migrate.reset',
        'MigrateRollback' => 'command.migrate.rollback',
        'MigrateStatus'   => 'command.migrate.status',
        'Optimize'        => 'command.optimize',
        'RouteCache'      => 'command.route.cache',
        'RouteClear'      => 'command.route.clear',
        'RouteList'       => 'command.route.list',
        'Up'              => 'command.up',
        'ViewClear'       => 'command.view.clear',
    ];

    /**
     * @var array
     */
    protected $devCommands = [
        'CacheTable'        => 'command.cache.table',
        'EventGenerate'     => 'command.event.generate',
        'JobMake'           => 'command.job.make',
        'MailMake'          => 'command.mail.make',
        'MigrateMake'       => 'command.migrate.make',
        'PolicyMake'        => 'command.policy.make',
        'QueueFailedTable'  => 'command.queue.failed-table',
        'QueueTable'        => 'command.queue.table',
        'SeederMake'        => 'command.seeder.make',
        'SessionTable'      => 'command.session.table',
        'Serve'             => 'command.serve',
        'TestMake'          => 'command.test.make',
        'VendorPublish'     => 'command.vendor.publish',
    ];

    /**
     * Register for service provider.
     */
    public function register()
    {
        $this->registerCommands($this->commands);
        $this->registerCommands($this->devCommands);
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            $method = "register{$command}Command";
            call_user_func_array([
                $this,
                $method,
            ], []);
        }
        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     */
    protected function registerAppNameCommand()
    {
        $this->app->singleton('command.app.name', function ($app) {
            return new AppNameCommand($app['composer'], $app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerCacheTableCommand()
    {
        $this->app->singleton('command.cache.table', function ($app) {
            return new CacheTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerClearCompiledCommand()
    {
        $this->app->singleton('command.clear-compiled', function () {
            return new ClearCompiledCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerClearResetsCommand()
    {
        $this->app->singleton('command.auth.resets.clear', function () {
            return new ClearResetsCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerConfigCacheCommand()
    {
        $this->app->singleton('command.config.cache', function ($app) {
            return new ConfigCacheCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerConfigClearCommand()
    {
        $this->app->singleton('command.config.clear', function ($app) {
            return new ConfigClearCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerEventGenerateCommand()
    {
        $this->app->singleton('command.event.generate', function () {
            return new EventGenerateCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerDownCommand()
    {
        $this->app->singleton('command.down', function () {
            return new DownCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerEnvironmentCommand()
    {
        $this->app->singleton('command.environment', function () {
            return new EnvironmentCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerJobMakeCommand()
    {
        $this->app->singleton('command.job.make', function ($app) {
            return new JobMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerKeyGenerateCommand()
    {
        $this->app->singleton('command.key.generate', function () {
            return new Commands\KeyGenerateCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerMailMakeCommand()
    {
        $this->app->singleton('command.mail.make', function ($app) {
            return new MailMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.migrate', function ($app) {
            return new MigrateCommand($app['migrator']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateInstallCommand()
    {
        $this->app->singleton('command.migrate.install', function ($app) {
            return new MigrateInstallCommand($app['migration.repository']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton('command.migrate.make', function ($app) {
            $creator = $app['migration.creator'];
            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateRefreshCommand()
    {
        $this->app->singleton('command.migrate.refresh', function () {
            return new MigrateRefreshCommand;
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateResetCommand()
    {
        $this->app->singleton('command.migrate.reset', function ($app) {
            return new MigrateResetCommand($app['migrator']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateRollbackCommand()
    {
        $this->app->singleton('command.migrate.rollback', function ($app) {
            return new MigrateRollbackCommand($app['migrator']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateStatusCommand()
    {
        $this->app->singleton('command.migrate.status', function ($app) {
            return new MigrateStatusCommand($app['migrator']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerOptimizeCommand()
    {
        $this->app->singleton('command.optimize', function ($app) {
            return new Commands\OptimizeCommand($app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerQueueFailedTableCommand()
    {
        $this->app->singleton('command.queue.failed-table', function ($app) {
            return new FailedTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerQueueTableCommand()
    {
        $this->app->singleton('command.queue.table', function ($app) {
            return new TableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerSeederMakeCommand()
    {
        $this->app->singleton('command.seeder.make', function ($app) {
            return new SeederMakeCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerSessionTableCommand()
    {
        $this->app->singleton('command.session.table', function ($app) {
            return new SessionTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerRouteCacheCommand()
    {
        $this->app->singleton('command.route.cache', function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerRouteClearCommand()
    {
        $this->app->singleton('command.route.clear', function ($app) {
            return new RouteClearCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerRouteListCommand()
    {
        $this->app->singleton('command.route.list', function ($app) {
            return new RouteListCommand($app['router']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerServeCommand()
    {
        $this->app->singleton('command.serve', function () {
            return new Commands\ServeCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerTestMakeCommand()
    {
        $this->app->singleton('command.test.make', function ($app) {
            return new TestMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerUpCommand()
    {
        $this->app->singleton('command.up', function () {
            return new Commands\UpCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerVendorPublishCommand()
    {
        $this->app->singleton('command.vendor.publish', function ($app) {
            return new VendorPublishCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerViewClearCommand()
    {
        $this->app->singleton('command.view.clear', function ($app) {
            return new Commands\ViewClearCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerPolicyMakeCommand()
    {
        $this->app->singleton('command.policy.make', function ($app) {
            return new Commands\PolicyMakeCommand($app['files']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        if ($this->app->environment('production')) {
            return array_values($this->commands);
        } else {
            return array_merge(array_values($this->commands), array_values($this->devCommands));
        }
    }
}
