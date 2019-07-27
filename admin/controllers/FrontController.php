<?php

/**
 * Front controller
 *
 * @package PLX
 * @author	Pedro "P3ter" CADETE based on Alejandro GERVASIO work (https://www.sitepoint.com/front-controller-pattern-1/)  
 **/
namespace controllers;

class FrontController
{
    const DEFAULT_CONTROLLER = __NAMESPACE__ . "AdminController";
    const DEFAULT_ACTION     = "index";

    private $_controller    = self::DEFAULT_CONTROLLER;
    private $_action        = self::DEFAULT_ACTION;
    private $_params        = array();

    public function __construct(array $options = array()) {
        if (empty($options)) {
            $this->parseUri();
        }
        else {
            if (isset($options["controller"])) {
                $this->setController($options["controller"]);
            }
            if (isset($options["action"])) {
                $this->setAction($options["action"]);
            }
            if (isset($options["params"])) {
                $this->setParams($options["params"]);
            }
        }
    }

    public function setController($controller) {
        $controller = __NAMESPACE__ . '\\' . ucfirst(strtolower($controller)) . 'Controller';
        if (!class_exists($controller)) {
            throw new \InvalidArgumentException(
                'The action controller' . $controller . 'has not been defined.');
        }
        $this->_controller = $controller;
        return $this;
    }

    public function setAction($action) {
        $reflector = new \ReflectionClass($this->controller);
        if (!$reflector->hasMethod($action)) {
            throw new \InvalidArgumentException(
                'The controller action' . $action . 'has been not defined.');
        }
        $this->_action = $action;
        return $this;
    }

    public function setParams(array $params) {
        $this->_params = $params;
        return $this;
    }

    public function run() {
        printf('<br>controller : ' . $this->_controller . '<br>');
        printf('action : ' . $this->_action . '<br>');
        printf('params : ' . $this->_params . '<br>');
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }
    
    private function parseUri() {
        $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
        $path = preg_replace('/[^a-zA-Z0-9]/', "", $path);
        @list($controller, $action, $params) = explode("/", $path, 3);
        if (isset($controller)) {
            $this->setController($controller);
        }
        if (isset($action)) {
            $this->setAction($action);
        }
        if (isset($params)) {
            $this->setParams(explode("/", $params));
        }
    }
}