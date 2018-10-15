<?php

namespace App\Services\Amazon\S3;

use Intervention\Image\Exception\ImageException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;


class ImageProcessor
{

    const NOT_VALID_EXCEPTION = 'Not a valid image format';
    const FORMAT_TYPE         = ['jpg', 'jpeg', 'png', 'gif', 'tif', 'bmp', 'ico', 'psd', 'webp', 'data-url'];

    protected $manager;
    protected $img;
    protected $format;
    protected $quality;
    protected $width;
    protected $name;

    /**
     * @var Image
     */
    private $pre;

    /**
     * ImageProcessor constructor.
     *
     * @param ImageManager|null $manager
     */
    public function __construct(ImageManager $manager = null)
    {
        $this->manager = $manager;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param        $name
     * @param        $img
     * @param string $format
     * @param int    $quality
     */
    public function setImage($name, $img, $format = 'jpg', $quality = 90): void
    {
        $this->img     = $img;
        $this->quality = $quality;
        $this->imgProcessor();
        $this->checkValidImageType();
        $this->setName($name);
        $this->setFormat($format);
    }

    /**
     * Check image type
     * @author HarikiRito <nxh0809@gmail.com>
     *
     */
    public function checkValidImageType(): void
    {
        if (!in_array($this->getExtension(), self::FORMAT_TYPE)) {
            throw new ImageException(self::NOT_VALID_EXCEPTION);
        }
    }

    /**
     * Get image extension
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed
     */
    public function getExtension(): string
    {
        return explode('/', $this->getMime())[1];
    }

    /**
     * Get image mime
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return string
     */
    public function getMime(): string
    {
        return $this->pre->mime();
    }

    /**
     * Create image processor
     * @author HarikiRito <nxh0809@gmail.com>
     *
     */
    public function imgProcessor(): void
    {
        $arr = explode(',', $this->img);

        if (count($arr) < 2) throw new ImageException(self::NOT_VALID_EXCEPTION);

        $img = $arr[1];
        $this->pre = $this->manager->make($img)->encode($this->format, $this->quality);
    }

    /**
     * Set image format
     * ['jpg', 'jpeg', 'png', 'gif', 'tif', 'bmp', 'ico', 'psd', 'webp', 'data-url']
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $format
     */
    public function setFormat($format = 'jpg'): void
    {
        $format = strtolower($format);

        if (in_array($format, self::FORMAT_TYPE)) {
            $this->format = $format;
            $this->imgProcessor();
            return;
        }

        throw new ImageException(self::NOT_VALID_EXCEPTION);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set image width
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * Set image quality
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int $quality
     */
    public function setQuality(int $quality): void
    {
        $this->quality = $quality;
    }

    /**
     * Get full image name with extension
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->name . '.' . $this->getExtension();
    }

    /**
     * Get image encode
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return string
     */
    public function getEncoded(): string
    {
        $img = $this->pre;

        $img = ($this->width !== null) ? $img->widen($this->width) : $img;

        return $img->encode($this->format, $this->quality)->getEncoded();
    }

}