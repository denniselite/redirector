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
     * If route could not be found redirect to main page on target host
     * In config:
     * ```
     *    'targetHost'
     * ```
     * @var bool
     */
    private $_isForceRedirect = false;

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        if ($err = $this->checkConfig($this->_config)) {
            throw new \Exception('Wrong configuration: ' . $err);
        }
        $currentURI = $_SERVER['REQUEST_URI'];
        $this->processURI($currentURI);
    }

    /**
     * @param string $currentURI
     */
    public function processURI($currentURI)
    {
        $inRoutes = isset($this->_config['routes'][$currentURI]);
        if ($inRoutes) {
            $link = $this->getLink($this->_config['routes'][$currentURI]);
            $this->sendResponse($link);
        } elseif ($this->_isForceRedirect) {
            $link = $this->getLink();
            $this->sendResponse($link);
        }
    }

    /**
     * @param $link
     */
    public function sendResponse($link)
    {
        header('Location: ' . $link);
        http_response_code(308);
        printf('<a href="%s">%s</a>', $link, $link);
        exit();
    }

    /**
     * @param [] $config
     * @return $this
     * @throws \Exception
     */
    public function setConfig($config)
    {
        if ($err = $this->checkConfig($config)) {
            throw new \Exception('Wrong configuration: ' . $err);
        }
        $this->_config = $config;
        return $this;
    }

    /**
     * @param bool $forceRedirect
     * @return $this
     */
    public function setIsForceRedirect($forceRedirect)
    {
        $this->_isForceRedirect = $forceRedirect;
        return $this;
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

    /**
     * @param [] $config
     * @return null|string
     */
    protected function checkConfig($config)
    {
        $error = null;
        if (!isset($config['targetHost']) || !is_string($config['targetHost'])) {
            $error = 'Param targetHost is invalid';
        }
        if (!isset($config['routes']) || !is_array($config['routes'])) {
            $error = 'Param routes is invalid';
        }
        if (!isset($config['forceRedirect']) || !is_bool($config['forceRedirect'])) {
            $error = 'Param forceRedirect is invalid';
        }
        return $error;
    }

    /**
     * @param $route
     * @return string
     */
    protected function getLink($route = '')
    {
        return $this->_config['targetHost'] . $route;
    }
}