# Redirector

This is a package for redirecting specific incoming requests to another resource.

### Config example

You should create `config.php` configuration file like this:

```php
return [
    'forceRedirect' => false,
    'targetHost' => 'target-host.local',
    'routes' => [
        '/some-article' => '/some-article-on-target'
    ]
];
```