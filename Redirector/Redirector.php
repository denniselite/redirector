<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 25/12/2017
 * Time: 16:29
 */

namespace Redirector;

class Redirector implements IRedirectable
{
    private $_config = [];

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->checkConfig()) {
            throw new \Exception('Invalid configuration');
        }
        $currentRoute = $_SERVER['REQUEST_URI'];
        echo $currentRoute;
    }

    /**
     * @return bool
     */
    protected function checkConfig()
    {
        $isActual = true;
        if (!isset($this->_config['targetHost']) || !is_string($this->_config['targetHost'])) {
            $isActual = false;
        }
        if (!isset($this->_config['routes']) || !is_array($this->_config['routes'])) {
            $isActual = false;
        }
        return $isActual;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setTargetHost($host)
    {
        $this->_config['targetHost'] = $host;
        return $this;
    }

    /**
     * @param [] $routes
     * @return $this
     */
    public function setRoutes($routes)
    {
        $this->_config['routes'] = $routes;
        return $this;
    }
}