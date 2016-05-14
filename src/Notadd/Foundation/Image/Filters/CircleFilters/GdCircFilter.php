<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2016-05-14 14:39
 */
namespace Notadd\Foundation\Image\Filters\CircleFilters;
use Notadd\Foundation\Image\Filters\GdFilter;
/**
 * Class GdCircFilter
 * @package Notadd\Foundation\Image\Filters\CircleFilters
 */
class GdCircFilter extends GdFilter {
    /**
     * @var array
     */
    protected $availableOptions = ['o'];
    /**
     * @return void
     */
    public function run() {
        $image = $this->draw($this->getMask());
        $this->driver->setOutputType('png');
        $this->driver->swapResource($image);
    }
    /**
     * @return mixed
     */
    protected function getMask() {
        $w = imagesx($this->driver->getResource());
        $h = imagesy($this->driver->getResource());
        $m = min($w, $h);
        $bg = imagecreatetruecolor($w, $h);
        $bg = $this->drawCirlce($bg, $w, $h, 255, 255, 255, 0);
        return $bg;
    }
    /**
     * @return mixed
     */
    protected function draw($mask) {
        // create new empty image
        $resource = $this->driver->getResource();
        $w = imagesx($resource);
        $h = imagesy($resource);
        $canvas = imagecreatetruecolor($w, $h);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        for($x = 0; $x < $w; $x++) {
            for($y = 0; $y < $h; $y++) {
                extract($this->pixelAt($resource, $x, $y));
                $alpha = $this->pixelAt($mask, $x, $y);
                $a = 127 - floor($alpha['r'] / 2);
                imagesetpixel($canvas, $x, $y, imagecolorallocatealpha($canvas, $r, $g, $b, $a));
            }
        }
        return $canvas;
    }
    /**
     * @param mixed $resource
     * @param mixed $x
     * @param mixed $y
     * @param mixed $alpha
     * @return mixed
     */
    protected function pixelAt($resource, $x, $y, $alpha = false) {
        $color = imagecolorat($resource, $x, $y);
        list($r, $g, $b, $a) = array_values((imagecolorsforindex($resource, $color)));
        return compact('r', 'g', 'b', 'a');
    }
    /**
     * @param mixed $canvas
     * @param mixed $w
     * @param mixed $h
     * @param mixed $r
     * @param mixed $g
     * @param mixed $b
     * @param mixed $opc
     * @param mixed $cirle
     * @return mixed
     */
    protected function drawCirlce($canvas, $w, $h, $r, $g, $b, $opc, $cirle = true) {
        $centerX = floor($w / 2);
        $centerY = floor($h / 2);
        if($cirle) {
            $w = $h = min($w, $h);
        }
        $offset = 2 * $this->getOption('o', 1);
        $xa = ($w - $offset) / 2;
        $xb = ($h - $offset) / 2;
        $thickness = 2.5 / min($xa, $xb);
        $color = imagecolorallocatealpha($canvas, $r, $g, $b, $opc);
        $maxTransparency = 127;
        for($x = 0; $x <= $xa + 1; $x++) {
            for($y = 0; $y <= $xb + 1; $y++) {
                // implicit formula: 1 = $x*$x/($a*$a) + $y*$y/($b*$b)
                $one = $x * $x / ($xa * $xa) + $y * $y / ($xb * $xb);
                $bound = ($one - 1) / $thickness;
                if($bound > 1) {
                    break;
                }
                $transparency = round(abs($bound) * $maxTransparency);
                $alpha = $bound < -($thickness) ? $color : $color | ($transparency << 24);
                // draw circle
                imagesetpixel($canvas, $centerX + $x, $centerY + $y, $alpha);
                imagesetpixel($canvas, $centerX - $x, $centerY + $y, $alpha);
                imagesetpixel($canvas, $centerX - $x, $centerY - $y, $alpha);
                imagesetpixel($canvas, $centerX + $x, $centerY - $y, $alpha);
            }
        }
        return $canvas;
    }
}