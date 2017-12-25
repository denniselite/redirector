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
     * @param string $host
     * @return $this
     * @throws \Exception
     */
    public function setTargetHost($host);

    /**
     * @param [] $routes
     * @return $this
     */
    public function setRoutes($routes);

    /**
     * @return void
     */
    public function run();
}