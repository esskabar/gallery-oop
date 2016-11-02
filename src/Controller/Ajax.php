<?php

namespace Controller;

use Html\View;
use Http\Request;
use Http\Response;
use Filesystem\File;

class Ajax
{
    public $view;

    public $file;

    public $request;

    public $response;

    public function __construct()
    {
        $this->view = new View();
        $this->file = new File();
        $this->request = new Request();
        $this->response = new Response();
    }

    public function loadAllImagesAction()
    {
        $data['all_images'] = $this->file->allFilesFolder();
        $data['folder_image'] = $this->file->smallImageFolder;
        $data['current_folder'] = $this->file->currentFolder;
        $this->view->generate('/layout/gallery.php', $data);
    }

    public function downloadImageAction()
    {
        $return = $this->file->downloadImage();
        $this->response->set('Content-Type', 'application/json');
        $this->response->setContent($this->response->messageResponse($return));
        $this->response->send();
    }

    public function removeFileAction()
    {
        $data = $this->request->getRequest('data');
        $return = $this->file->removeFile($data);
        $this->response->set('Content-Type', 'application/json');
        $this->response->setContent($this->response->messageResponse($return));
        $this->response->send();
    }

    public function createFolderAction()
    {
        $request = new Request();
        $return = $this->file->createFolder($request->getRequest('data'));
        $this->response->set('Content-Type', 'application/json');
        $this->response->setContent($this->response->messageResponse($return));
        $this->response->send();
    }

    
    public function renameFolderAction()
    {
        $request = new Request();
        $return = $this->file->renameFolder($request->getRequest());
        $this->response->set('Content-Type', 'application/json');
        $this->response->setContent($this->response->messageResponse($return));
        $this->response->send();
    }
}
