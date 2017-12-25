<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 25/12/2017
 * Time: 16:30
 */

namespace Redirector;

interface IRedirectable
{
    /**
     * @param string $currentURI
     */
    public function processURI($currentURI);

    /**
     * @return void
     */
    public function run();

    /**
     * @param [] $config
     * @return $this
     * @throws \Exception
     */
    public function setConfig($config);

    /**
     * @param [] $routes
     * @return $this
     */
    public function setRoutes($routes);

    /**
     * @param string $host
     * @return $this
     * @throws \Exception
     */
    public function setTargetHost($host);
}