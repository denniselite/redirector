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
        $redirectURI = $this->getTargetLinkKey($currentURI);
        if (is_string($redirectURI)) {
            $link = $this->getLink($this->_config['routes'][$redirectURI]);
            $this->sendResponse($link);
        } elseif ($this->_isForceRedirect) {
            $link = $this->getLink();
            $this->sendResponse($link);
        }
    }

    /**
     * @param string $requestedURI
     * @return null|string
     */
    public function getTargetLinkKey($requestedURI)
    {
        $currentURIs = array_keys($this->_config['routes']);
        $parsedRequestedURI = parse_url($requestedURI);

        if (isset($parsedRequestedURI['query'])) {
            return $this->getByDeepCompare($currentURIs, $parsedRequestedURI);
        } else {
            return (isset($this->_config['routes'][$requestedURI]))
                ? $this->_config['routes'][$requestedURI]
                : null;
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
     * Compare queries without arguments order; e.g.
     *  ```
     *      /index.php?id=101&Itemid=562&option=com_content&view=article
     *      /index.php?option=com_content&view=article&id=101&Itemid=562
     *  ```
     *  will be equal. Return correct route key if exists.
     *
     * @param $currentURIs
     * @param $parsedRequestedQuery
     * @return null|string
     */
    private function getByDeepCompare(&$currentURIs, &$parsedRequestedQuery)
    {
        $key = null;
        foreach ($currentURIs as $uri) {
            $parsedURI = parse_url($uri);
            if (isset($parsedURI['query'])) {

                // split strings by delimiter '&' and find the Zero diff
                $requestedURIArgs = explode('&', $parsedRequestedQuery['query']);
                sort($requestedURIArgs);
                $uriArgs = explode('&', $parsedURI['query']);
                sort($uriArgs);
                $result = array_diff($uriArgs, $requestedURIArgs);
                if (count($result) === 0) {
                    $key = $uri;
                    break;
                }
            }
        }
        return $key;
    }

    /**
     * @param $route
     * @return string
     */
    protected function getLink($route = '')
    {
        return $this->_config['targetHost'] . $route;
    }

    /**
     * Convert from CSV file to 'key' => 'value' array for configuration. E.g. next following links
     * ```
     *    http://old-domain.com/;http://new-domain.com/
     *    http://old-domain.com/some-article;http://new-domain.com/another-article
     * ```
     * will convert to
     * ```
     *    '/' => '/',
     *    'some-article' => 'another-article'
     * ```
     * Is is a helper for routes configuration management
     *
     * @param resource $file
     * @return array
     * @throws \Exception
     */
    public static function getRoutesFromCSV($file)
    {
        if (!is_resource($file)) {
            throw new \Exception('getRoutesFromCSV: input value is not a Resource');
        }
        $arrayConfig = [];
        while (!feof($file)) {

            // Create the row as array with table date values
            $rowString = fgets($file);
            $routePair = explode(';', $rowString);

            // Remove empty columns
            foreach ($routePair as $key => $value) {
                if (empty($value)) {
                    unset($routePair[$key]);
                }
            }

            // Process only "pairs" arrays
            if (count($routePair) != 2) {
                continue;
            }

            // Remove domains
            foreach ($routePair as $key => $value) {
                $value = trim($value);
                $url = parse_url($value);
                $uriQuery = (isset($url['query'])) ? '?' . $url['query'] : '';
                $uriPath = (isset($url['path'])) ? $url['path'] : '/';
                $routePair[$key] = $uriPath . $uriQuery;
            }

            $arrayConfig[$routePair[0]] = $routePair[1];
        }
        return $arrayConfig;
    }
}