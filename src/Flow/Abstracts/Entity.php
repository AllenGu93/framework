<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-01 15:55
 */
namespace Notadd\Foundation\Flow\Abstracts;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Flow\FlowBuilder;
use Notadd\Foundation\Permission\PermissionManager;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class Entity.
 */
abstract class Entity extends FlowBuilder
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Entity constructor.
     */
    public function __construct()
    {
        $this->container = $this->getContainer();
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    abstract public function name();

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    abstract public function places();

    /**
     * Definition of transitions for flow.
     *
     * @return array
     */
    abstract public function transitions();

    /**
     * Announce a transition.
     */
    public function announce()
    {
    }

    /**
     * Enter a place.
     *
     * @param \Symfony\Component\Workflow\Event\Event $event
     */
    public function enter(Event $event)
    {
    }

    /**
     * Entered a place.
     */
    public function entered()
    {
    }

    /**
     * Guard a transition.
     *
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     */
    abstract public function guard(GuardEvent $event);

    /**
     * Leave a place.
     */
    public function leave()
    {
    }

    /**
     * Into a transition.
     */
    public function transition()
    {
    }

    /**
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     * @param bool                                         $permission
     */
    protected function block(GuardEvent $event, bool $permission)
    {
        if ($permission) {
            $event->setBlocked(false);
        } else {
            $event->setBlocked(true);
        }
    }

    /**
     * @return array
     */
    public function events()
    {
        $collection = new Collection();
        $name = method_exists($this, 'name') ? $this->{'name'}() : 'unnamed';
        $places = method_exists($this, 'places') ? $this->{'places'}() : [];
        if (method_exists($this, 'transitions')) {
            $transitions = $this->{'transitions'}();
            $transitions = collect($transitions)->transform(function (Transition $transition) {
                return $transition->getName();
            })->toArray();
        } else {
            $transitions = [];
        }
        foreach ($places as $place) {
            $collection->put('flow.' . $name . '.enter', 'enter');
            $collection->put('flow.' . $name . '.enter.' . $place, 'enter' . ucfirst($place));
            $collection->put('flow.' . $name . '.entered', 'entered');
            $collection->put('flow.' . $name . '.entered.' . $place, 'entered' . ucfirst($place));
            $collection->put('flow.' . $name . '.leave', 'leave');
            $collection->put('flow.' . $name . '.leave.' . $place, 'leave' . ucfirst($place));
        }
        foreach ($transitions as $transition) {
            $collection->put('flow.' . $name . '.announce', 'announce');
            $collection->put('flow.' . $name . '.announce.' . $transition, 'announce' . ucfirst($transition));
            $collection->put('flow.' . $name . '.guard', 'guard');
            $collection->put('flow.' . $name . '.guard.' . $transition, 'guard' . ucfirst($transition));
            $collection->put('flow.' . $name . '.transition', 'transition');
            $collection->put('flow.' . $name . '.transition.' . $transition, 'transition' . ucfirst($transition));
        }

        return $collection->toArray();
    }

    /**
     * @return \Illuminate\Container\Container|\Notadd\Foundation\Application
     */
    protected function getContainer()
    {
        return Container::getInstance();
    }

    /**
     * @param $identification
     *
     * @param $group
     *
     * @return bool
     */
    protected function permission($identification, $group)
    {
        if ($group instanceof Model) {
            $group = $group->getAttribute('identification');
        } else if ($group instanceof Collection) {
            $group = $group->transform(function (Model $group) {
                return $group->getAttribute('identification');
            })->toArray();
        }
        foreach ((array)$group as $item) {
            if ($this->container->make(PermissionManager::class)->check($identification, $item)) {
                return true;
            }
        }

        return false;
    }
}
