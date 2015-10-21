<?php

namespace Kalnoy\LaravelCommon\Images;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ImageData
 *
 * @method ImageData resize($width, $height)
 * @method ImageData fitAspectRatio($size, $ratio)
 *
 * @package App\Images
 */
class ImageData implements Arrayable {

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @param string $publicPath
     * @param int|null $width
     * @param int|null $height
     */
    public function __construct($publicPath, $width = null, $height = null)
    {
        $this->publicPath = $publicPath;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get asset path to the image.
     *
     * @return string
     */
    public function asset()
    {
        return asset($this->publicPath);
    }

    /**
     * Render HTML element.
     *
     * @param string|null $alt
     * @param array $attributes
     * @param bool $secure
     *
     * @return string
     */
    public function img($alt = null, array $attributes = [], $secure = false)
    {
        if ($this->width) $attributes['width'] = $this->width;
        if ($this->height) $attributes['height'] = $this->height;

        return \HTML::image($this->publicPath, $alt, $attributes, $secure);
    }

    /**
     * Get full path to the image.
     *
     * @return string
     */
    public function getPath()
    {
        return public_path($this->publicPath);
    }

    /**
     * @return string
     */
    public function getPublicPath()
    {
        return $this->publicPath;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Ensure that image has size info.
     */
    public function ensureHasSize()
    {
        if ($this->width !== null && $this->height !== null) return;

        if (false === $data = getimagesize($this->getPath()))
        {
            throw new \RuntimeException('Could not get the size of image.');
        }

        list($this->width, $this->height) = $data;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromProcessorData(array $data)
    {
        return new static($data['publicPath'], $data['size'][0], $data['size'][1]);
    }

    /**
     * @param string $publicPath
     *
     * @return ImageData|null
     */
    public static function fromFile($publicPath)
    {
        if ( ! $image = getimagesize(public_path($publicPath))) return null;

        return new static($publicPath, $image[0], $image[1]);
    }

    /**
     * @param $serialized
     *
     * @return null|ImageData
     */
    public static function unserialize($serialized)
    {
        if ($serialized instanceof static) return $serialized;

        if (empty($serialized)) return null;

        list($path, $width, $height) = json_decode($serialized, true);

        return new static($path, $width, $height);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        $this->ensureHasSize();

        return json_encode([ $this->publicPath, $this->width, $this->height ]);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'src' => $this->publicPath,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return ImageData
     */
    public function __call($method, $parameters)
    {
        array_unshift($parameters, $this);

        return call_user_func_array([ app('images'), $method ], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    function __toString()
    {
        return $this->serialize();
    }

}