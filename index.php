<?php
require_once 'config.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = CONTROLLER_PATH . '/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    die("Controlador no encontrado");
}

require_once $controllerFile;
$controllerObject = new $controllerName();

if (!method_exists($controllerObject, $action)) {
    die("Acción no encontrada");
}

$controllerObject->$action();