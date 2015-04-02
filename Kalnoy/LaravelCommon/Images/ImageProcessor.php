<?php

namespace Kalnoy\LaravelCommon\Images;

use Closure;
use Exception;
use Illuminate\Log\Writer;
use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image processor is reponsible for making avatars and thumbnails.
 */
class ImageProcessor {

    /**
     * The image processor.
     *
     * @var \Intervention\Image\ImageManager
     */
    protected $image;

    /**
     * Filesystem object,
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * The path to where save images.
     *
     * @var string
     */
    protected $path;

    /**
     * @var Writer
     */
    protected $log;

    /**
     * Image max width when uploading.
     *
     * @var int
     */
    public $maxWidth = 1280;

    /**
     * Image max height when uploading.
     *
     * @var int
     */
    public $maxHeight = 1024;

    /**
     * Background color for uploaded image.
     *
     * @var string
     */
    public $background = 'ffffff';

    /**
     * Init processor.
     *
     * @param ImageManager $image
     * @param Filesystem $file
     * @param string $path
     */
    public function __construct(ImageManager $image, Filesystem $file, $path)
    {
        $this->image = $image;
        $this->file = $file;
        $this->path = $path;
    }

    /**
     * Resize a image to fit a square of given length.
     *
     * @param string $src
     * @param int $length
     *
     * @return string
     */
    public function square($src, $length)
    {
        return $this->resize($src, $length, $length);
    }

    /**
     * Resize image to a given maximum width and height.
     *
     * @param string $src
     * @param int|null $width
     * @param int|null $height
     *
     * @return string
     */
    public function resize($src, $width, $height)
    {
        return $this->process('resize', $src, [ $width, $height ], function ($image, $w, $h)
        {
            return $image->resize($w, $h, function (Constraint $constraint)
            {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        });
    }

    /**
     * Fit an image into image of specified size keeping aspect without cropping.
     *
     * @param string $src
     * @param int $width
     * @param int $height
     * @param mixed $background
     *
     * @return string
     */
    public function fit($src, $width, $height, $background = null)
    {
        $params = [ $width, $height, $background ];

        return $this->process('fit', $src, $params, function (Image $image, $w, $h, $bg)
        {
            $image = $image->resize($w, $h, function (Constraint $constraint)
            {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if ($image->width < $w or $image->height < $h)
            {
                $image = $image->resizeCanvas($w, $h, null, false, $bg);
            }

            return $image;
        });
    }

    /**
     * Fit image into a specified width keeping aspect ratio not greater than specified.
     *
     * @param string $src
     * @param int $width
     * @param int $ratio
     *
     * @return string
     */
    public function fitAspectRatio($src, $width, $ratio)
    {
        return $this->process('fitar', $src, [ $width, $ratio ], function (Image $image, $width, $ratio)
        {
            $iRatio = $image->width() / ($ih = $image->height());

            if ($iRatio > $ratio)
            {
                $image->resizeCanvas(ceil($ih * $ratio), $ih, 'center');
            }

            if ($image->width() > $width)
            {
                $image->widen($width);
            }

            return $image;
        });
    }

    /**
     * Fit image to a square of specified length keeping aspect ratio without
     * cropping.
     *
     * @param string $src
     * @param int $length
     * @param mixed $background
     *
     * @return string
     */
    public function fitToSquare($src, $length, $background = null)
    {
        return $this->fit($src, $length, $length, $background);
    }

    /**
     * Cut a square from image and fit to the square of specified length.
     *
     * @param string $src
     * @param int $length
     * @param int $x The x coordinate of the cropping center
     * @param int $y The y coordinate of the cropping center
     * @param int $halfLength Half-length of the cropping square
     *
     * @return string
     */
    public function cropToFitSquare($src, $length, $x, $y, $halfLength)
    {
        return $this->process('crop', $src, [ $length ], function ($image, $length) use ($x, $y, $halfLength)
        {
            $image = $image->crop($halfLength * 2, $halfLength * 2, $x - $halfLength, $y - $halfLength);

            // Resize to the given length allowing upsizing
            $image = $image->resize($length, $length);

            return $image;
        });
    }

    /**
     * Process an image and save the results.
     *
     * @param string $category
     * @param string $src
     * @param Closure $processor
     *
     * @return string|bool
     */
    public function process($category, $src, $params, Closure $processor)
    {
        return $this->save($src, $processor, $params, null, $category.implode('', $params));
    }

    /**
     * @param $src
     * @param $processor
     * @param array $params
     * @param null $ext
     * @param null $category
     *
     * @return array|bool
     *
     * @throws Exception
     */
    protected function save($src, $processor, $params = [], $ext = null, $category = null)
    {
        if (empty($src) or ! $this->file->exists($src)) return false;

        if ( ! $ext)
        {
            $ext = pathinfo($src, PATHINFO_EXTENSION);
        }

        $publicPath = $this->getFileName($this->hash($src), $ext, $category);
        $path = $this->getFullFileName($publicPath);

        try
        {
            $image = $this->image->make($src);

            array_unshift($params, $image);

            call_user_func_array($processor, $params);

            $image->save($path);

            $size = [ $image->width(), $image->height() ];

            $image->destroy();

            return compact('publicPath', 'path', 'size');
        }

        catch (Exception $e)
        {
            if (isset($image)) $image->destroy();

            throw $e;
        }
    }

    /**
     * Upload an image.
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    public function upload(UploadedFile $file)
    {
        return $this->save($file->getPathname(), [ $this, 'processUploadedImage' ], [], 'jpg');
    }

    /**
     * Generate a filename for storage.
     *
     * @param string $baseName
     * @param string $ext
     * @param string|null $category
     *
     * @return string
     */
    public function getFileName($baseName, $ext, $category = null)
    {
        $category = $category ? $this->hash($baseName.$category) : $this->hash($baseName);

        return $this->path.'/'.substr($category, 0, 4).'/'.$baseName.'.'.$ext;
    }

    /**
     * @param string $src
     *
     * @return string
     */
    public function getFullFileName($src)
    {
        $src = public_path($src);

        $directory = pathinfo($src, PATHINFO_DIRNAME);

        // Make sure that target directory exists
        if ( ! $this->file->isDirectory($directory))
        {
            $this->file->makeDirectory($directory, 0777, true);
        }

        return $src;
    }

    /**
     * Compute a hash for the string.
     *
     * @param string $value
     *
     * @return string
     */
    protected function hash($value)
    {
        return sprintf('%x', crc32($value));
    }

    /**
     * Get an underlying image processor.
     *
     * @return \Intervention\Image\Image
     */
    public function getProcessor()
    {
        return $this->image;
    }

    /**
     * @param Writer $log
     */
    public function setLogger(Writer $log)
    {
        $this->log = $log;
    }

    /**
     * @param $image
     */
    protected function processUploadedImage(Image $image)
    {
        $image->resize($this->maxWidth, $this->maxHeight, function (Constraint $constraint)
        {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        if ($this->background)
        {
            $image->resizeCanvas($image->width(), $image->height(), null, false, $this->background);
        }
    }

}