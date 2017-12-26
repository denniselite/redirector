# Redirector

This is a package for redirecting specific incoming HTTP-requests to another resource.

Features:

 - 'True' redirect status 308 Permanent Redirect ([RFC 7538](https://tools.ietf.org/html/rfc7538))
 - Force and soft redirecting by unhandled links;
 - Works via and without composer;
 - CVS files parsing with links mapping supported;
 - Deep matching by query params - compare queries without arguments order; e.g.
```
  /index.php?id=101&Itemid=562&option=com_content&view=article
  /index.php?option=com_content&view=article&id=101&Itemid=562
```
will be equal.

### Config example

You should create `config.php` configuration file like this:

```php
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
```

where:

* forceRedirect - redirect user even route is not found in configuration
* targetHost - target web-site domain for user redirecting
* routes - key-value set, where __key__ is a link from current web-site and __value__ is a link to page on the new web-site

Warning! Keys and values for routes map should start with '/'! 

### CSV links processing

You also can use CSV files with 2 columns for old-new website links matching via `getRoutesFromCSV($file)` method.

Convert from CSV file to 'key' => 'value' array for configuration. E.g. next following links

 ```
    http://old-domain.com/;http://new-domain.com/
    http://old-domain.com/some-article;http://new-domain.com/another-article
 ```

 will convert to

 ```php
 [
    '/' => '/',
    'some-article' => 'another-article'
 ];   
 ```

It is a helper for routes configuration management; as example:

```php
$csvRoutes = fopen('links.csv', 'r');
$routesSet = Redirector\Redirector::getRoutesFromCSV($csvRoutes);
fclose($csvRoutes);
$config['routes'] = $routesSet;
(new \Redirector\Redirector)->setConfig($config)->run();
```
 
==============================================================

### Usage examples

##### Via composer
1. Add this into *composer.json*
```
    "repositories": [
        { "type": "vcs", "url": "https://github.com/denniselite/redirector"}
    ],
    "require": {
        "denniselite/redirector" : "dev-master"
    }
```

2. After that run `composer install` command and use it before the application running:
```php
(new \Redirector\Redirector)->setConfig($config)->run();
```

##### Without composer

If your project doesn't support the *composer* manager (wordpress, joomla, ...):

1. Init composer in your project directory using the `composer init` command;
2. Add in composer.json:

```
...
    "repositories": [
        { "type": "vcs", "url": "https://github.com/denniselite/redirector"}
    ],
    "require": {
        "denniselite/redirector" : "dev-master"
    }
...    
```

3. Run `composer install`;
4. Add into your start-script, e.g. index.php following code:

```php
require_once "vendor/autoload.php";
$config = require_once "config.example.php";
(new \Redirector\Redirector)->setConfig($config)->run();
```  

5. Done!

You also are able to use only zip-downloading mechanism for this package usage. For it you should import src files from package:

```php
require_once "Redirector/Redirector.php";
require_once "Redirector/IRedirectable.php";
$config = require_once "config.example.php";
(new \Redirector\Redirector)->setConfig($config)->run();
```

But it isn't convenient for version management and code supporting.

PPS Be careful about `vendor/` directory and configuration files - they should be hidden for HTTP requests ;)