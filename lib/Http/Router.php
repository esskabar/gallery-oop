<?php

namespace Http;

class Router
{
    /** @var string $controllerNamespace */
    protected $controllerNamespace = '\Controller';

    /** @var string $defaultController */
    public $defaultController = '\Index';

    /** @var string $defaultAction */
    public $defaultAction = 'index';

    protected $notFound = 'notFound';

    public function dispatch()
    {
        $routeInfo = $this->pathInfo();

        $controllerClass = count($routeInfo) > 1
            ? $this->getClassFromRoute($routeInfo[0])
            : $this->defaultController;
        $controllerClass = $this->controllerNamespace . $controllerClass;

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
        } else {
            $controller = new $this->controllerNamespace.$this->defaultController;
        }



        $actionName = count($routeInfo) > 1
            ? $this->getActionFromRoute($routeInfo[1])
            : $this->defaultAction;
        $actionName .= 'Action';

        if (method_exists($controllerClass, $actionName)) {
            $controller->$actionName();
        } else {
            $defaultMethod = $this->notFound.'Action';
            $controller->$defaultMethod();
        }

        // @TODO: pass Request object to the action if it requires it

    }

    /**
     * @return array
     */
    public function pathInfo()
    {
        $url = trim(urldecode(substr($_SERVER['REQUEST_URI'], 1)), '/');

        return explode('/', $url);
    }

    /**
     * @return array|bool
     */
    public function currentUrl()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $str = str_replace('http://' . $_SERVER['SERVER_NAME'] . '/', '', $_SERVER['HTTP_REFERER']);
            $url = trim(urldecode(substr($str, 1)), '/');
            return explode('/', $url);
        } else {
            return false;
        }

    }

    /**
     * @param string $path
     * @return string
     */
    public function getClassFromRoute($path)
    {
        return '\\' . str_replace(' ', '\\', ucwords(str_replace('_', ' ', $path)));
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getActionFromRoute($path)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $path)));
    }

    /**
     * @param $param
     * @return array
     */
    public function getUrlParam($param)
    {
        return explode('/', $param);
    }
}
