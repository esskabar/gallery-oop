<?php


namespace Filesystem;

use Http\Request;
use Http\Response;

class File
{

    public $response;
    /**
     * @var int
     */
    protected $widthImage = 1024;

    /**
     * @var int
     */
    protected $heightImage = 768;

    /**
     * @var int
     */
    public $widthSmallImage = 150;

    /**
     * @var int
     */
    public $heightSmallImage = 150;

    /**
     * @var string
     */
    public $smallImageFolder = '/gallery/small';

    /**
     * @var string
     */
    public $rootSmallImageFolder = '';

    /**
     * @var string
     */
    public $normalImageFolder = '/gallery';

    /**
     * @var string
     */
    public $rootNormalImageFolder = '';

    /**
     * @var string
     */
    public $currentFolder = '/';

    /**
     * @var string
     */
    protected $rootFolder;

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
     * @var array File extension
     */
    public $fileExtensionFormAllowed = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    public function __construct()
    {
        $this->rootSmallImageFolder = getcwd(). $this->smallImageFolder;
        $this->rootNormalImageFolder = getcwd(). $this->normalImageFolder;
        $request = new Request();
        $this->response = new Response();
        $param = $request->getCurrentParameters('folder');
        $param = str_replace('_', '/', $param);
        if ($param) {
            $this->currentFolder = DS. $param. DS;
        }
    }

    /**
     * @return array|string
     */
    public function allFilesFolder()
    {
        $request = new Request();
        $path = getcwd(). $this->smallImageFolder. $this->currentFolder;
        if (file_exists($path)) {
            $dir = new \DirectoryIterator($path);
            $folderByFolder = [];
            $fileByFolder = [];
            $iterator = 0;
            $iteratorFile = 1000;
            while ($dir->valid()) {
                $file = $dir->current();
                if ($file->isDir()) {
                    if (!$file->isDot()) {
                        $folderByFolder[ $iterator ]['file'] = $file->getFilename();
                        $folderByFolder[ $iterator ]['file_name'] = $file->getFilename();
                        $folderByFolder[ $iterator ]['url'] = $request->getUrlGallery($file->getFilename());
                        $iterator++;
                    }
                }
                if ($file->isFile()) {
                    if (in_array($file->getExtension(), $this->fileExtensionAllowed)) {
                        $fileByFolder[ $iteratorFile ]['file'] = $file->getFilename();
                        $fileByFolder[ $iteratorFile ]['file_name'] = str_replace(
                            '.' . $file->getExtension(),
                            '',
                            $file->getFilename()
                        );
                        $iteratorFile++;
                    };
                }
                $dir->next();
            }
            $return = array_merge($folderByFolder, $fileByFolder);
            return $return;
        } else {
            return $this->response->messageResponse('Folder not exist', false);
        }
    }

    /**
     * @return bool|string
     */
    public function downloadImage()
    {
        $images = new Image();
        $file = $_FILES['imagefile'];
        if (in_array($file['type'], $this->fileExtensionFormAllowed)) {
            $rate = $images->imageRate($file);
            if ($rate[0] < $this->widthImage || $rate[1] < $this->heightImage) {
                return $this->saveImage($file);
            } else {
                return $this->saveImage($file, true);
            }
        } else {
            return $this->response->messageResponse('file type is not allowed', false);
        }
    }

    /**
     * @param      $image
     * @param bool $needResize
     * @return bool
     */
    public function saveImage($image, $needResize = false)
    {
        $images = new Image();

        $result = $images->resizeImage(
            $image,
            $this->rootSmallImageFolder . $this->currentFolder,
            $this->widthSmallImage,
            $this->heightSmallImage,
            true
        );

        if ($result) {
            if ($needResize) {
                $result = move_uploaded_file(
                    $image['tmp_name'],
                    $this->rootNormalImageFolder. $this->currentFolder. $image['name']
                );
            } else {
                $result = $images->resizeImage(
                    $image,
                    $this->rootNormalImageFolder. $this->currentFolder,
                    $this->widthImage,
                    $this->heightImage
                );
            }
            if ($result) {
                return $this->response->messageResponse('image saved');
            } else {
                return $this->response->messageResponse('Small image don\'t saved', false);
            }
        } else {
            return $this->response->messageResponse('Small image don\'t saved', false);
        }
    }

    /**
     * @param $folderName
     * @return string
     */
    public function createFolder($folderName)
    {
        if (!empty($folderName)) {
            if (file_exists($this->rootSmallImageFolder. $this->currentFolder. DS. $folderName)
                || file_exists($this->rootNormalImageFolder. $this->currentFolder. DS. $folderName)) {
                return $this->response->messageResponse('folder don\t created! folder exist', false);
            }
            $small = !mkdir($this->rootSmallImageFolder. $this->currentFolder. DS. $folderName, 0777, true);
            $big = !mkdir($this->rootNormalImageFolder. $this->currentFolder. DS. $folderName, 0777, true);
            if ($small && $big) {
                return $this->response->messageResponse('Folder not created', false);
            }
            return $this->response->messageResponse('Folder created');
        } else {
            return $this->response->messageResponse('folder not must empty', false);
        }
    }

    /**
     * @param $file
     * @return string
     */
    public function removeFile($file)
    {
        $smallImage = '';
        $bigImage = '';
        $select_elem = $file;
        if (is_dir($this->rootNormalImageFolder. $this->currentFolder. DS. $select_elem)) {
            $bigImage = rmdir($this->rootNormalImageFolder. $this->currentFolder. DS. $select_elem);
            $smallImage = rmdir($this->rootSmallImageFolder. $this->currentFolder. DS. $select_elem);
        }
        if (is_file($this->rootNormalImageFolder. $this->currentFolder. DS. $select_elem)) {
            $smallImage = unlink($this->rootSmallImageFolder. $this->currentFolder. DS. $select_elem);
            $bigImage = unlink($this->rootNormalImageFolder. $this->currentFolder. DS. $select_elem);
        }
        if ($smallImage && $bigImage) {
            return $this->response->messageResponse('Removed file\\directory', false);
        } else {
            return $this->response->messageResponse('Error removed file\\directory', false);
        }
    }

    /**
     * @param $params
     * @return string
     */
    public function renameFolder($params)
    {
        $newName = $params['newname'];
        $oldName = $params['oldname'];
        $returnNew = rename(
            $this->rootNormalImageFolder. $this->currentFolder. $oldName,
            $this->rootNormalImageFolder. $this->currentFolder. $newName
        );
        $returnOew = rename(
            $this->rootSmallImageFolder. $this->currentFolder. $oldName,
            $this->rootSmallImageFolder. $this->currentFolder. $newName
        );
        if ($returnNew && $returnOew) {
            return $this->response->messageResponse('Created');
        } else {
            return $this->response->messageResponse('Created', false);
        }
    }
}
