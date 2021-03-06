<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-08-29 14:07
 */
namespace Notadd\Foundation\Extension;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Notadd\Foundation\Configuration\Repository as ConfigurationRepository;

/**
 * Class ExtensionManager.
 */
class ExtensionManager
{
    /**
     * @var \Notadd\Foundation\Configuration\Repository
     */
    protected $configuration;

    /**
     * @var \Illuminate\Container\Container|\Notadd\Foundation\Application
     */
    protected $container;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $extensions;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $unloaded;

    /**
     * ExtensionManager constructor.
     *
     * @param \Illuminate\Container\Container             $container
     * @param \Notadd\Foundation\Configuration\Repository $configuration
     * @param \Illuminate\Events\Dispatcher               $events
     * @param \Illuminate\Filesystem\Filesystem           $files
     */
    public function __construct(Container $container, ConfigurationRepository $configuration, Dispatcher $events, Filesystem $files)
    {
        $this->configuration = $configuration;
        $this->container = $container;
        $this->events = $events;
        $this->extensions = new Collection();
        $this->files = $files;
        $this->unloaded = new Collection();
    }

    /**
     * Get a extension by name.
     *
     * @param $name
     *
     * @return \Notadd\Foundation\Extension\Extension
     */
    public function get($name)
    {
        return $this->extensions->get($name);
    }

    /**
     * Path for extension.
     *
     * @return string
     */
    public function getExtensionPath()
    {
        return $this->container->basePath() . DIRECTORY_SEPARATOR . $this->configuration->get('extension.directory');
    }

    /**
     * Extensions of enabled.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEnabledExtensions()
    {
        $list = new Collection();
        if ($this->getExtensions()->isEmpty()) {
            return $list;
        }
        $this->extensions->each(function (Extension $extension) use ($list) {
            $extension->isEnabled() && $list->push($extension);
        });

        return $list;
    }

    /**
     * Extension list.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getExtensions()
    {
        if ($this->extensions->isEmpty() && $this->container->isInstalled()) {
            if ($this->files->isDirectory($this->getExtensionPath())) {
                collect($this->files->directories($this->getExtensionPath()))->each(function ($vendor) {
                    collect($this->files->directories($vendor))->each(function ($directory) {
                        if ($this->files->exists($file = $directory . DIRECTORY_SEPARATOR . 'composer.json')) {
                            $package = new Collection(json_decode($this->files->get($file), true));
                            $identification = Arr::get($package, 'name');
                            $type = Arr::get($package, 'type');
                            if ($type == 'notadd-extension' && $identification) {
                                $provider = '';
                                if ($entries = data_get($package, 'autoload.psr-4')) {
                                    foreach ($entries as $namespace => $entry) {
                                        $provider = $namespace . 'Extension';
                                    }
                                }
                                if ($this->files->exists($autoload = $directory . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
                                    $this->files->requireOnce($autoload);
                                }
                                $authors = Arr::get($package, 'authors');
                                $description = Arr::get($package, 'description');
                                if (class_exists($provider)) {
                                    $extension = new Extension($identification);
                                    $extension->setAuthor($authors);
                                    $extension->setDescription($description);
                                    $extension->setDirectory($directory);
                                    $extension->setEnabled($this->container->isInstalled() ? $this->container->make('setting')->get('extension.' . $identification . '.enabled', false) : false);
                                    $extension->setInstalled($this->container->isInstalled() ? $this->container->make('setting')->get('extension.' . $identification . '.installed', false) : false);
                                    $extension->setEntry($provider);
                                    method_exists($provider, 'description') && $extension->setDescription(call_user_func([$provider, 'description']));
                                    method_exists($provider, 'name') && $extension->setName(call_user_func([$provider, 'name']));
                                    method_exists($provider, 'script') && $extension->setScript(call_user_func([$provider, 'script']));
                                    method_exists($provider, 'stylesheet') && $extension->setStylesheet(call_user_func([$provider, 'stylesheet']));
                                    method_exists($provider, 'version') && $extension->setVersion(call_user_func([$provider, 'version']));
                                    $this->extensions->put($identification, $extension);
                                } else {
                                    $this->unloaded->put($identification, [
                                        'authors'        => $authors,
                                        'description'    => $description,
                                        'directory'      => $directory,
                                        'identification' => $identification,
                                        'provider'       => $provider,
                                    ]);
                                }
                            }
                        }
                    });
                });
            }
        }

        return $this->extensions;
    }

    /**
     * Modules of installed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInstalledExtensions()
    {
        $list = new Collection();
        if ($this->getExtensions()->isNotEmpty()) {
            $this->extensions->each(function (Extension $extension) use ($list) {
                $extension->isInstalled() && $list->put($extension->getIdentification(), $extension);
            });
        }

        return $list;
    }

    /**
     * Modules of not-installed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNotInstalledExtensions()
    {
        $list = new Collection();
        if ($this->getExtensions()->isNotEmpty()) {
            $this->extensions->each(function (Extension $extension) use ($list) {
                $extension->isInstalled() || $list->put($extension->getIdentification(), $extension);
            });
        }

        return $list;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUnloadedExtensions()
    {
        return $this->unloaded;
    }

    /**
     * Check for extension exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->extensions->has($name);
    }

    /**
     * Vendor Path.
     *
     * @return string
     */
    public function getVendorPath()
    {
        return $this->container->basePath() . DIRECTORY_SEPARATOR . 'vendor';
    }
}
