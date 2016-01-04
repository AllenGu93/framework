<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-01 00:05
 */
namespace Notadd\Category\Events;
use Illuminate\Support\Collection;
use Notadd\Category\Models\Category;
/**
 * Class GetCategoryAdminTemplates
 * @package Notadd\Category\Events
 */
class GetCategoryAdminTemplates {
    /**
     * @var \Notadd\Category\Models\Category
     */
    private $category;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $templates;
    /**
     * GetCategoryAdminTemplates constructor.
     * @param \Notadd\Category\Models\Category $category
     * @param \Illuminate\Support\Collection $templates
     */
    public function __construct(Category $category, Collection $templates) {
        $this->category = $category;
        $this->templates = $templates;
    }
    /**
     * @param $key
     * @param $value
     */
    public function register($key, $value) {
        $this->templates->put($key, $value);
    }
}