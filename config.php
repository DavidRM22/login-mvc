<?php
session_start();

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('MODEL_PATH', APP_PATH . '/models');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('DATA_PATH', BASE_PATH . '/data');
define('MAIL_FROM_EMAIL', getenv('MAIL_FROM_EMAIL') ?: 'no-reply@login-mvc.local');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Login MVC');
define('SMTP_HOST', getenv('SMTP_HOST') ?: '');
define('SMTP_PORT', (int)(getenv('SMTP_PORT') ?: 587));
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');
define('SMTP_TIMEOUT', (int)(getenv('SMTP_TIMEOUT') ?: 10));


function redirect($url)
{
    header("Location: $url");
    exit;
}

function route($controller, $action)
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    return $script . '?controller=' . urlencode($controller) . '&action=' . urlencode($action);
}

function asset($file)
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    if ($scriptDir === '.' || $scriptDir === DIRECTORY_SEPARATOR) {
        $scriptDir = '';
    }

    return rtrim($scriptDir, '/\\') . '/' . ltrim($file, '/');
}

function isLoggedIn()
{
  return isset($_SESSION['user_id']);

}

date_default_timezone_set('America/Lima');

function authRequired()
{
    if (!isset($_SESSION['user_id'])) {
        redirect(route('auth', 'login'));
    }
}

