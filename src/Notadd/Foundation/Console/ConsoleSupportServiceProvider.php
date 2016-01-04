<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 00:07
 */
namespace Notadd\Foundation\Console;
use Illuminate\Auth\GeneratorServiceProvider as AuthGeneratorServiceProvider;
use Illuminate\Console\ScheduleServiceProvider;
use Illuminate\Routing\GeneratorServiceProvider as RoutingGeneratorServiceProvider;
use Illuminate\Support\AggregateServiceProvider;
use Notadd\Foundation\Composer\ComposerServiceProvider;
use Notadd\Foundation\Database\MigrationServiceProvider;
use Notadd\Foundation\Database\SeedServiceProvider;
use Notadd\Foundation\Queue\ConsoleServiceProvider as QueueConsoleServiceProvider;
use Notadd\Foundation\Session\ConsoleServiceProvider as SessionConsoleServiceProvider;
/**
 * Class ConsoleSupportServiceProvider
 * @package Notadd\Foundation\Console
 */
class ConsoleSupportServiceProvider extends AggregateServiceProvider {
    /**
     * @var bool
     */
    protected $defer = true;
    /**
     * @var array
     */
    protected $providers = [
        AuthGeneratorServiceProvider::class,
        ScheduleServiceProvider::class,
        ComposerServiceProvider::class,
        MigrationServiceProvider::class,
        SeedServiceProvider::class,
        QueueConsoleServiceProvider::class,
        RoutingGeneratorServiceProvider::class,
        SessionConsoleServiceProvider::class,
    ];
}