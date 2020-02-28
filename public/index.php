<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false;
$trustedProxies = $trustedProxies ? explode(',', $trustedProxies) : [];

// APP_TRUST_PROXY is not a standard Symfony env variable.
// If it is set, then we add the remote address as a trusted proxy. This is safe and appropriate
// for some deployment setups, like Heroku, but not for all. Use cautiously.
// See: https://symfony.com/doc/current/deployment/proxies.html
// And: https://devcenter.heroku.com/articles/deploying-symfony4#trusting-the-heroku-router
if($_SERVER['APP_ENV'] == 'prod' && $_SERVER['APP_TRUST_PROXY']) $trustedProxies[] = $_SERVER['REMOTE_ADDR'];
if($trustedProxies) {
    Request::setTrustedProxies($trustedProxies, Request::HEADER_X_FORWARDED_AWS_ELB);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
