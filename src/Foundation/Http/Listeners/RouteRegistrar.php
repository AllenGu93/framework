<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-10-21 15:29
 */
namespace Notadd\Foundation\Http\Listeners;

use Notadd\Foundation\Http\Controllers\IndexController;
use Notadd\Foundation\Routing\Abstracts\RouteRegistrar as AbstractRouteRegistrar;

/**
 * Class RouteRegistrar.
 */
class RouteRegistrar extends AbstractRouteRegistrar
{
    /**
     * @return void
     */
    public function handle()
    {
        $this->router->group(['middleware' => ['web']], function () {
            $this->router->resource('/', IndexController::class, [
                'only' => 'index',
            ]);
        });
    }
}
