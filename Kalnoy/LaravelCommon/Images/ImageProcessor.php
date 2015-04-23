<?php

namespace Kalnoy\LaravelCommon\Images;

use Kalnoy\LaravelCommon\Images\ImageData;
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
     * @param string|ImageData $image
     * @param int $length
     *
     * @return string
     */
    public function square($image, $length)
    {
        return $this->resize($image, $length, $length);
    }

    /**
     * Resize image to a given maximum width and height.
     *
     * @param string|ImageData $image
     * @param int|null $width
     * @param int|null $height
     *
     * @return string
     */
    public function resize($image, $width, $height)
    {
        return $this->process($image, [ $width, $height ], function ($image, $w, $h)
        {
            return $image->resize($w, $h, function (Constraint $constraint)
            {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

        }, 'resize');
    }

    /**
     * Fit an image into image of specified size keeping aspect without cropping.
     *
     * @param string|ImageData $image
     * @param int $width
     * @param int $height
     * @param mixed $background
     *
     * @return string
     */
    public function fit($image, $width, $height, $background = null)
    {
        $params = [ $width, $height, $background ];

        return $this->process($image, $params, function (Image $image, $w, $h, $bg)
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

        }, 'fit');
    }

    /**
     * Fit image into a specified width keeping aspect ratio not greater than specified.
     *
     * @param string|ImageData $image
     * @param int $width
     * @param int $ratio
     *
     * @return string
     */
    public function fitAspectRatio($image, $width, $ratio)
    {
        return $this->process($image, [ $width, $ratio ], function (Image $image, $width, $ratio)
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

        }, 'fitar');
    }

    /**
     * Fit image to a square of specified length keeping aspect ratio without
     * cropping.
     *
     * @param string|ImageData $image
     * @param int $length
     * @param mixed $background
     *
     * @return string
     */
    public function fitToSquare($image, $length, $background = null)
    {
        return $this->fit($image, $length, $length, $background);
    }

    /**
     * Cut a square from image and fit to the square of specified length.
     *
     * @param string|ImageData $image
     * @param int $size
     * @param int $x The x coordinate of the cropping center
     * @param int $y The y coordinate of the cropping center
     * @param int $halfLength Half-length of the cropping square
     *
     * @return string
     */
    public function cropToFitSquare($image, $size, $x, $y, $halfLength)
    {
        return $this->process($image, [ $size ], function (Image $image, $length) use ($x, $y, $halfLength)
        {
            return $this->cropToFitSquareImage($image, $length, $x, $y, $halfLength);

        }, 'crop');
    }

    /**
     * @param Image $image
     * @param $size
     * @param $x
     * @param $y
     * @param $halfLength
     *
     * @return Image
     */
    protected function cropToFitSquareImage(Image $image, $size, $x, $y, $halfLength)
    {
        $image = $image->crop($halfLength * 2, $halfLength * 2, $x - $halfLength, $y - $halfLength);

        // Resize to the given length allowing upsizing
        return $image->resize($size, $size);
    }

    /**
     * Process an image and save the results.
     *
     * @param string|ImageData $src
     * @param mixed $params
     * @param callable $processor
     * @param string $category
     * @param string $ext
     *
     * @return ImageData
     *
     * @throws Exception
     */
    public function process($src, $params, $processor, $category = '', $ext = '')
    {
        if ($src instanceof ImageData) $src = $src->getPath();

        if (empty($src) or ! $this->file->exists($src)) return false;

        $image = $this->image->make($src);

        try
        {
            array_unshift($params, $image);

            $image = call_user_func_array($processor, $params);

            $data = $this->save($image, $category.implode('', (array)$params), $src, $ext);
        }

        finally
        {
            $image->destroy();
        }

        return $data;
    }

    /**
     * @param Image $image
     * @param string $src
     * @param string $category
     * @param string $ext
     *
     * @return ImageData
     */
    protected function save(Image $image, $category = '', $src = '', $ext = null)
    {
        $ext = $this->getExtension($src, $ext);

        $publicPath = $this->getPublicPath(str_random(8), $ext, $category);

        $path = $this->getFileName($publicPath);

        $image->save($path);

        $size = $image->getSize();

        return new ImageData($publicPath, $size->width, $size->height);
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
        return $this->process($file->getPathname(), [], [ $this, 'processUploadedImage' ], '', 'jpg');
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
    public function getPublicPath($baseName, $ext, $category = null)
    {
        $category = $category ? $this->hash($baseName.$category) : $this->hash($baseName);

        return $this->path.'/'.substr($category, 0, 4).'/'.$baseName.'.'.$ext;
    }

    /**
     * @param string $src
     *
     * @return string
     */
    public function getFileName($src)
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
     *
     * @return Image
     */
    protected function processUploadedImage(Image $image)
    {
        $image = $image->resize($this->maxWidth, $this->maxHeight, function (Constraint $constraint)
        {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        if ($this->background)
        {
            $image = $image->resizeCanvas($image->width(), $image->height(), null, false, $this->background);
        }

        return $image;
    }

    /**
     * @param string $src
     * @param string $ext
     *
     * @return string
     */
    protected function getExtension($src, $ext)
    {
        if ($ext) return $ext;

        if ($ext = pathinfo($src, PATHINFO_EXTENSION)) return $ext;

        return 'jpg';
    }

}