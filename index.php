<?php

require_once 'vendor/autoload.php';
session_start();
var_dump($_SESSION);

$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/login', 'App\Controllers\LoginController@showForm');
    $r->addRoute('GET', '/logout', 'App\Controllers\LoginController@logout');
    $r->addRoute('POST', '/login', 'App\Controllers\LoginController@authUser');
    $r->addRoute('GET', '/auth', 'App\Controllers\LoginController@auth');
    $r->addRoute('GET', '/registration', 'App\Controllers\RegistrationController@showForm');
    $r->addRoute('GET', '/', 'App\Controllers\RegistrationController@showForm');
    $r->addRoute('POST', '/registration', 'App\Controllers\RegistrationController@storeUser');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo "Page not found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo "Method not allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = explode("@", $handler);

        $container = new DI\Container();
        $container->set(\App\Repositories\UsersRepository::class, \DI\create(\App\Repositories\MySQLUsersRepository::class));

        $response = ($container->get($controller)->$method());

        $loader = new \Twig\Loader\FilesystemLoader('app/View');
        $twig = new \Twig\Environment($loader);
        $twig->addGlobal('errors', $_SESSION['errors'] ?? []);

        if ($response instanceof \App\View\View) {
            unset($_SESSION['errors']);
            $template = $twig->load($response->getPathToTemplate());
            echo $template->render($response->getData());
            exit;
        }

        if ($response instanceof \App\Redirect) {
            header('Location: ' . $response->getLocation());
        }
        break;
}