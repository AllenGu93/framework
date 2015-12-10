<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Support\Arr;
use Notadd\Admin\AdminServiceProvider;
use Notadd\Article\ArticleServiceProvider;
use Notadd\Category\CategoryServiceProvider;
use Notadd\Foundation\Auth\Models\User;
use Notadd\Foundation\Console\ConsoleServiceProvider;
use Notadd\Foundation\Console\ConsoleSupportServiceProvider;
use Notadd\Foundation\Console\Kernel as ConsoleKernel;
use Notadd\Foundation\Http\HttpServiceProvider;
use Notadd\Foundation\Http\Kernel as HttpKernel;
use Notadd\Foundation\Install\Kernel as InstallKernel;
use Notadd\Foundation\Exceptions\Handler;
use Notadd\Install\InstallServiceProvider;
use Notadd\Menu\MenuServiceProvider;
use Notadd\Page\PageServiceProvider;
use Notadd\Theme\ThemeServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
class Server {
    private $application;
    private $path;
    public function __construct($path) {
        define('NOTADD_START', microtime(true));
        $this->path = realpath($path);
        $this->application = new Application($this->path);
    }
    public function init() {
        $config = Arr::collapse([
            $this->loadIlluminateConfiguration(),
            $this->loadFiledConfiguration()
        ]);
        $this->application->instance('env', 'production');
        $this->application->instance('config', new Repository($config));
        $this->application->instance('version', Application::VERSION);
        $this->application->registerConfiguredProviders();
        $this->application->singleton(HttpKernelContract::class, HttpKernel::class);
        $this->application->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
        $this->application->singleton(ExceptionHandler::class, Handler::class);
        if($this->application->isInstalled()) {
            $this->application->register(ThemeServiceProvider::class);
            $this->application->register(MenuServiceProvider::class);
            $this->application->register(CategoryServiceProvider::class);
            $this->application->register(ArticleServiceProvider::class);
            $this->application->register(HttpServiceProvider::class);
            $this->application->register(PageServiceProvider::class);
            $this->application->register(AdminServiceProvider::class);
        } else {
            $this->application->register(InstallServiceProvider::class);
        }
        return $this;
    }
    /**
     * @return string
     */
    protected function loadFiledConfiguration() {
        $file = realpath($this->application->storagePath() . '/framework/notadd') . DIRECTORY_SEPARATOR . 'config.php';
        if(file_exists($file)) {
            return require $file;
        } else {
            return [];
        }
    }
    protected function loadIlluminateConfiguration() {
        return [
            'app' => [
                'debug' => true,
                'url' => 'http://localhost',
                'timezone' => 'UTC+8',
                'locale' => 'en',
                'fallback_locale' => 'en',
                'key' => 'GERojpSdTnQQbr77s5iXIa1c7Ne7NO4d',
                'cipher' => MCRYPT_RIJNDAEL_128,
                'log' => 'daily'
            ],
            'auth' => [
                'driver' => 'eloquent',
                'model' => User::class,
                'table' => 'users',
                'password' => [
                    'email' => 'admin::emails.password',
                    'table' => 'password_resets',
                    'expire' => 60,
                ],
            ],
            'cache' => [
                'default' => 'file',
                'stores' => [
                    'file' => [
                        'driver' => 'file',
                        'path' => $this->application->storagePath() . '/framework/cache',
                    ],
                ],
                'prefix' => 'flarum',
            ],
            'filesystems' => [
                'default' => 'local',
                'cloud' => 's3',
                'disks' => []
            ],
            'mail' => [
                'driver' => 'mail',
            ],
            'session' => [
                'driver' => 'file',
                'lifetime' => 120,
                'expire_on_close' => false,
                'encrypt' => false,
                'files' => $this->application->storagePath() . '/framework/sessions',
                'connection' => null,
                'table' => 'sessions',
                'lottery' => [2, 100],
                'cookie' => 'notadd_session',
                'path' => '/',
                'domain' => null,
                'secure' => false,
            ],
            'view' => [
                'paths' => [],
                'compiled' => $this->application->storagePath() . '/framework/views',
            ]
        ];
    }
    public function terminate() {
        $kernel = $this->application->make(HttpKernelContract::class);
        $response = $kernel->handle($request = Request::capture());
        $response->send();
        $kernel->terminate($request, $response);
    }
    public function console() {
        $this->application->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
        $this->application->singleton(ExceptionHandler::class, Handler::class);
        $kernel = $this->application->make(ConsoleKernelContract::class);
        $status = $kernel->handle($input = new ArgvInput, new ConsoleOutput);
        $kernel->terminate($input, $status);
        exit($status);
    }
}