<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 25/12/2017
 * Time: 16:17
 */

return [

    // redirect user to target even route is not found in configuration
    'forceRedirect' => false,

    // target web-site domain for user redirecting
    'targetHost' => 'target-host.local',

    // key-value set, "link from current web-site" => "link to page on the new web-site"
    // every link should start with '/'
    'routes' => [
        '/some-article' => '/some-article-on-target'
    ]
];