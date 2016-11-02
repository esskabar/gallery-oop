<?php


namespace Filesystem;

//use Filesystem\File;

use Http\Response;

class Image
{
    const IMAGE_JPG = 'image/jpeg';
    const IMAGE_GIF = 'image/gif';
    const IMAGE_PNG = 'image/png';

    protected $response;

    /**
     * @var int
     */
    protected $maxWidthImage = 1024;

    /**
     * @var int
     */
    protected $maxHeightImage = 768;

    /**
     * @var int
     */
    public $widthSmallImage = 150;

    /**
     * @var int
     */
    public $heightSmallImage = 150;

    /**
     * @var array
     */
    public $fileExtensionAllowed = [
        'jpg',
        'jpeg',
        'png',
        'gif',
    ];

    /**
     * @var array
     */
    public $fileExtensionFormAllowed = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * @param      $image
     * @param      $path
     * @param      $width
     * @param      $height
     * @param bool $thumbnail
     * @return bool
     */
    public function resizeImage($image, $path, $width, $height, $thumbnail = false)
    {
        $rate = $this->imageRate($image);
        if ($thumbnail) {
            $ratio = max($width / $rate[0], $height / $rate[1]);
        } else {
            $ratio = min($width / $rate[0], $height / $rate[1]);
        }

        $newHeight = ceil($rate[1] * $ratio);
        $newWidth = ceil($rate[0] * $ratio);
        $imageResult = imagecreatetruecolor($newWidth, $newHeight);
        $source = $this->createNewImage($image, $image['type']);
        if ($source != false) {
            imagecopyresized($imageResult, $source, 0, 0, 0, 0, $newWidth, $newHeight, $rate[0], $rate[1]);
            if ($thumbnail) {
                $thumb2 = imagecreatetruecolor($this->widthSmallImage, $this->heightSmallImage);
                imagecopy($thumb2, $imageResult, 0, 0, 0, 0, $newWidth, $newHeight);
                return $this->outputNewImage($thumb2, $image['type'], $image['name'], $path);
            } else {
                return $this->outputNewImage($imageResult, $image['type'], $image['name'], $path);
            }
        } else {
            return $this->response->messageResponse('Image don\'t created', false);
        }
    }

    /**
     * @param $image
     * @param $type
     * @return resource
     */
    public function createNewImage($image, $type)
    {
        switch ($type) {
            case self::IMAGE_JPG:
                return imagecreatefromjpeg($image['tmp_name']);
            case self::IMAGE_PNG:
                return imagecreatefrompng($image['tmp_name']);
            case self::IMAGE_GIF:
                return imagecreatefromgif($image['tmp_name']);
        }
        return false;
    }

    /**
     * @param $file
     * @return array
     */
    public function imageRate($file)
    {
        if (is_object($file)) {
            return [$file->width_image, $file->height_image];
        } else {
            list($widthImage, $heightImage) = getimagesize($file['tmp_name']);
            return [$widthImage, $heightImage];
        }
    }

    /**
     * @param $image
     * @param $type
     * @param $filename
     * @param $path
     * @return bool
     */
    public function outputNewImage($image, $type, $filename, $path)
    {
        switch ($type) {
            case self::IMAGE_JPG:
                $flag = imagejpeg($image, $path . $filename);
                break;
            case self::IMAGE_PNG:
                $flag = imagepng($image, $path . $filename);
                break;
            case self::IMAGE_GIF:
                $flag = imagegif($image, $path . $filename);
                break;
            default:
                $flag = false;
        }
        if ($flag) {
            return $this->response->messageResponse('Image Saved');
        } else {
            return $this->response->messageResponse('Image don\'t saved', false);
        }
    }
}
