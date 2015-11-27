<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-11 0:56:56
 */
namespace Notadd\Article;
use Illuminate\Support\Str;
use Notadd\Article\Models\Article as ArticleModel;
use Notadd\Category\Category;
class Article {
    /**
     * @var
     */
    private $id;
    /**
     * @var
     */
    private $model;
    /**
     * Article constructor.
     * @param $id
     */
    public function __construct($id) {
        $this->id = $id;
        $this->model = ArticleModel::findOrFail($id);
    }
    /**
     * @return mixed
     */
    public function getArticleId() {
        return $this->id;
    }
    /**
     * @return mixed
     */
    public function getCategory() {
        return new Category($this->model->getAttribute('category_id'));
    }
    /**
     * @return mixed
     */
    public function getContent() {
        return $this->model->getAttribute('content');
    }
    /**
     * @param int $limit
     * @return string
     */
    public function getDescription($limit = 130) {
        return Str::limit(strip_tags($this->model->getAttribute('content')), $limit, '...');
    }
    /**
     * @return mixed
     */
    public function getModel() {
        return $this->model;
    }
    /**
     * @return string
     */
    public function getRouting() {
        $category = new Category($this->model->getAttribute('category_id'));
        $path = $category->getRouting();
        return $path . '/' . $this->id;
    }
    public function getShowTemplate() {
        return $this->model->getShowTemplate();
    }
    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->model->getAttribute('title');
    }
}