<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-10-22 15:07
 */
namespace Notadd\Foundation\Queue;

use Illuminate\Queue\QueueServiceProvider as IlluminateQueueServiceProvider;

/**
 * Class QueueServiceProvider.
 */
class QueueServiceProvider extends IlluminateQueueServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;
}
