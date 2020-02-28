<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../var/bootstrap.php.cache';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
if ($request->getScheme() === 'http') {
    $urlRedirect = str_replace($request->getScheme(), 'https', $request->getUri());
    $response = new RedirectResponse($urlRedirect);
} else {
    $response = $kernel->handle($request);
}
$response->send();
$kernel->terminate($request, $response);
