<?php


namespace Controller;

use \Html\View;
use \Filesystem\File;

class Index
{
    public $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function indexAction()
    {
        $file = new File();
        $this->view->generate('/layout/main.php', $file);
    }

    public function folderAction()
    {
        $file = new File();
        $this->view->generate('/layout/main.php', $file);
    }

    public function notFoundAction()
    {
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
    }
}
