<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, iBenchu.org
 * @datetime 2017-02-10 17:09
 */
namespace Notadd\Foundation\Image\Commands;

/**
 * Class AbstractCommand.
 */
abstract class AbstractCommand
{
    /**
     * @var array
     */
    public $arguments;

    /**
     * @var mixed
     */
    protected $output;

    /**
     * @param \Notadd\Foundation\Image\Image $image
     *
     * @return mixed
     */
    abstract public function execute($image);

    /**
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param int $key
     *
     * @return \Notadd\Foundation\Image\Commands\Argument
     */
    public function argument($key)
    {
        return new Argument($this, $key);
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output ? $this->output : null;
    }

    /**
     * @return bool
     */
    public function hasOutput()
    {
        return !is_null($this->output);
    }

    /**
     * @param mixed $value
     */
    public function setOutput($value)
    {
        $this->output = $value;
    }
}
