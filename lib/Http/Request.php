<?php

namespace Http;

class Request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';


    /**
     * @param string $param
     * @return array|mixed
     */
    public function getParameters($param = '')
    {
        $route = new Router();
        $parameters = $route->pathInfo();
        unset($parameters[0], $parameters[1]);

        return $this->parseParam($parameters, $param);
    }

    /**
     * @param string $param
     * @return array|mixed
     */
    public function getCurrentParameters($param = '')
    {
        $route = new Router();
        $parameters = $route->currentUrl();
        unset($parameters[0], $parameters[1]);

        return $this->parseParam($parameters, $param);
    }

    /**
     * @param        $parameters
     * @param string $param
     * @return array|mixed
     */
    protected function parseParam($parameters, $param = '')
    {
        $args = [];
        $keyArgs = '';
        if (count($parameters) && is_array($parameters)) {
            $tmp = [];
            foreach ($parameters as $key => $val) {
                $keyStrip = filter_var($key, FILTER_SANITIZE_ENCODED);
                $tmp[$keyStrip] = filter_var($val, FILTER_SANITIZE_ENCODED);
            }
            foreach ($tmp as $key => $item) {
                if ($key % 2 != 0) {
                    $args[ $keyArgs ] = $item;
                } else {
                    $keyArgs = $item;
                }
            }

            if (empty($param)) {
                return $args;
            } else {
                return isset($args[ $param ])
                    ? $args[ $param ]
                    : $args;
            }
        } else {
            return [];
        }
    }

    /**
     * @param string $param
     * @return array|mixed
     */
    public function getRequest($param = '')
    {
        $parameters = $_POST;
        $args = [];
        if (count($parameters) && is_array($parameters)) {
            foreach ($parameters as $key => $val) {
                $keyStrip = filter_var($key, FILTER_SANITIZE_ENCODED);
                $args[ $keyStrip ] = filter_var($val, FILTER_SANITIZE_ENCODED);
            }
            if (empty($param)) {
                return $args;
            } else {
                return isset($args[ $param ])
                    ? $args[ $param ]
                    : $args;
            }
        } else {
            return [];
        }
    }

    /**
     * @param string $folder
     * @return string
     */
    public function getUrlGallery($folder = '')
    {
        $catalog = [];
        $param = $this->getCurrentParameters('folder');
        if ($param != null) {
            $catalog = explode('_', $param);
        }
        if ($folder == "..") {
            if (count($catalog) >= 1) {
                $countCatalog = count($catalog) - 1;
                unset($catalog[ $countCatalog ]);

                return $this->getUrl('index/index', ['folder' => $this->implodeParams($catalog)]);
            } else {
                return $this->baseUrl();
            }
        } else {
            $catalog[] = $folder;

            return $this->getUrl('index/index', ['folder' => $this->implodeParams($catalog)]);
        }
    }

    /**
     * @param       $classMethod
     * @param array $parameters
     * @return string
     */
    public function getUrl($classMethod, $parameters = [])
    {
        $router = new Router();
        $url = $this->baseUrl();
        $checkMethod = explode('/', $classMethod);
        if (!method_exists($checkMethod[0], $checkMethod[1])) {
            $classMethod = $router->defaultController . DS . $router->defaultAction;
        }
        if (count($parameters)) {
            foreach ($parameters as $key => $parameter) {
                if ($parameter == null) {
                    $url .= strtolower($classMethod);
                } else {
                    $url .= strtolower($classMethod) . DS . $key . DS . $parameter;
                }
            }
        } else {
            if ($classMethod) {
                $url .= $classMethod;
            }
        }

        return $url;
    }

    /**
     * @return string
     */
    public function baseUrl()
    {
        return 'http://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * @param array $params
     * @return string
     */
    public function implodeParams($params = [])
    {
        $params = array_filter($params);
        if ($params != null) {
            if (count($params) > 1) {
                return implode("_", $params);
            } else {
                $params = array_values($params);

                return $params[0];
            }
        } else {
            return '';
        }

    }
}
