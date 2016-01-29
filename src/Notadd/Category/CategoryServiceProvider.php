<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:46
 */
namespace Notadd\Category;
use Illuminate\Support\ServiceProvider;
use Notadd\Category\Category;
use Notadd\Category\Listeners\BeforeCategoryDelete;
use Notadd\Category\Models\Category as CategoryModel;
use Notadd\Foundation\Traits\InjectEventsTrait;
use Notadd\Foundation\Traits\InjectRouterTrait;
/**
 * Class CategoryServiceProvider
 * @package Notadd\Category
 */
class CategoryServiceProvider extends ServiceProvider {
    use InjectEventsTrait, InjectRouterTrait;
    /**
     * @return void
     */
    public function boot() {
        $this->getEvents()->listen('router.before', function() {
            $categories = CategoryModel::whereEnabled(true)->get();
            foreach($categories as $value) {
                if($value->alias) {
                    $category = new Category($value->id);
                    $this->getRouter()->get($category->getRouting() . '/{id}', 'Notadd\Article\Controllers\ArticleController@show')->where('id', '[0-9]+');
                    $this->getRouter()->get($category->getRouting(), function() use ($category) {
                        return $this->app->call('Notadd\Category\Controllers\CategoryController@show', ['id' => $category->getId()]);
                    });
                }
            }
        });
        $this->getRouter()->group(['namespace' => 'Notadd\Category\Controllers'], function () {
            $this->getRouter()->group(['middleware' => 'auth.admin', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
                $this->getRouter()->resource('category', 'CategoryController');
                $this->getRouter()->post('category/{id}/status', 'CategoryController@status');
            });
            $this->getRouter()->resource('category', 'CategoryController');
        });
        $this->getEvents()->subscribe(BeforeCategoryDelete::class);
    }
    /**
     * @return void
     */
    public function register() {
    }
}