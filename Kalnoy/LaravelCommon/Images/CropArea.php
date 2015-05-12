<?php

namespace Kalnoy\LaravelCommon\Images;

use Intervention\Image\Image;
use Intervention\Image\Size;

class CropArea {

    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $rotate;

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     */
    public function __construct($x, $y, $width, $height, $rotate = 0)
    {
        $this->x = (int)$x;
        $this->y = (int)$y;
        $this->width = (int)$width;
        $this->height = (int)$height;
        $this->rotate = $rotate ? (int)$rotate : 0;
    }

    /**
     * @param $value
     *
     * @return null|CropArea
     */
    public static function parse($value)
    {
        if (empty($value)) return null;

        list($x, $y, $width, $height, $rotate) = explode(',', $value, 5);

        return new static($x, $y, $width, $height, $rotate);
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply(Image $image)
    {
        if ($this->rotate) $image = $image->rotate(-$this->getRotateAngle());

        return $image->crop($this->width, $this->height, $this->x, $this->y);
    }

    /**
     * @return float
     */
    public function getRatio()
    {
        return $this->width / (float)$this->height;
    }

    /**
     * @return int
     */
    public function getRotateAngle()
    {
        return $this->rotate * 90;
    }

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setRatio($value)
    {
        $ratio = $this->getRatio();

        if ($ratio == $value) return $this;

        if ($value > $ratio)
        {
            $height = (int)($this->width / $value);

            $this->y += (int)(($this->height - $height) / 2);
            $this->height = $height;
        }
        else
        {
            $width = (int)($this->height * $value);

            $this->x += (int)(($this->width - $width) / 2);
            $this->width = $width;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param array $data
     *
     * @return CropArea
     */
    public static function fromArray(array $data)
    {
        if (count($data) < 4) return null;

        return new static($data[0], $data[1], $data[2], $data[3], isset($data[4]) ? $data[4] : 0);
    }

    /**
     * @param Image $image
     * @param $width
     * @param int $ratio
     *
     * @return CropArea
     */
    public static function offCenter(Image $image, $width = null, $ratio = 1)
    {
        /** @var Size $size */
        $size = $image->getSize();

        if (is_null($width)) $width = min($size->width, $size->height);

        $height = $width / $ratio;

        $x = ($size->width - $width) / 2;
        $y = ($size->height - $height) / 2;

        return new static($x, $y, $width, $height);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [ $this->x, $this->y, $this->width, $this->height, $this->rotate ];
    }

}