<?php

namespace Kalnoy\LaravelCommon\Images;

use Illuminate\Contracts\Support\Arrayable;
use Intervention\Image\Image;
use Intervention\Image\Size;

class CropArea implements Arrayable
{
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
     * @param int $rotate
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
        if ($this->rotate) $image = $image->rotate(-$this->rotate);

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
    public function getRotate()
    {
        return $this->rotate;
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

        if ($value > $ratio) {
            $height = (int)($this->width / $value);

            $this->y += (int)(($this->height - $height) / 2);
            $this->height = $height;
        } else {
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
        return new static(
            array_get($data, 'x', 0),
            array_get($data, 'y', 0),
            array_get($data, 'width', 0),
            array_get($data, 'height', 0),
            array_get($data, 'rotate', 0)
        );
    }

    /**
     * @param ImageData $image
     * @param $width
     * @param int $ratio
     *
     * @return CropArea
     */
    public static function offCenter(ImageData $image, $width = null, $ratio = 1
    ) {
        if (is_null($width)) $width = min($image->getWidth(), $image->getHeight());

        $height = $width / $ratio;

        $x = ($image->getWidth() - $width) / 2;
        $y = ($image->getHeight() - $height) / 2;

        return new static($x, $y, $width, $height);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height,
            'rotate' => $this->rotate,
        ];
    }

    /**
     * @param string $serialized
     *
     * @return CropArea|null
     */
    public static function unserialize($serialized)
    {
        if ($serialized instanceof static) return $serialized;

        if (empty($serialized)) return null;

        $d = json_decode($serialized);

        return new static($d[0], $d[1], $d[2], $d[3], $d[4]);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return json_encode([
                               $this->x,
                               $this->y,
                               $this->width,
                               $this->height,
                               $this->rotate
                           ]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * @param CropArea $other
     *
     * @return bool
     */
    public function eq(CropArea $other)
    {
        return  $this->x == $other->x && $this->y == $other->y &&
                $this->width == $other->width && $this->height == $other->height &&
                $this->rotate == $other->rotate;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->width > 0 && $this->height > 0;
    }

}