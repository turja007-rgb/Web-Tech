<?php
// public/index.php
declare(strict_types=1);
// Define project root
define("ROOT", __DIR__);
// Setup Error Logging
$logFilePath = ROOT . "/error.log";
if (!file_exists($logFilePath)) {
    if (touch($logFilePath)) {
        chmod($logFilePath, 0644);
    } else {
        die('Unable to create the log file.');
    }
}
error_reporting(E_ALL);
ini_set('log_errors', (string)1);
ini_set('error_log', $logFilePath);
//Error Logging End
session_start();

spl_autoload_register(function($class){
    $classPath = ROOT . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($classPath)) require $classPath;
});

$cfg = require ROOT . '/config/config.php';
// --------- Auth bootstrap from remember-me cookie ----------
if (empty($_SESSION['customer_id']) && !empty($_COOKIE['remember_me'])) {
    [$uid, $exp, $sig] = explode('|', $_COOKIE['remember_me']) + [null,null,null];
    if ($uid && $exp && $sig && (int)$exp > time()) {
        $expected = hash_hmac('sha256', $uid . '|' . $exp, $cfg['secret_key']);
        if (hash_equals($expected, $sig)) {
            $_SESSION['customer_id'] = (int)$uid;
        }
    }
}
// -----------------------------------------------------------
$routes = [];
function route($method, $path, $handler) {  //remove- (callable) $handler
    global $routes;
    if ($handler) { $routes[] = [$method,$path,$handler]; return; }
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $cfg = require ROOT . '/config/config.php';
    $base = rtrim($cfg['app_url'], '/');
    $path = ($base && str_starts_with($uri, $base)) ? substr($uri, strlen($base)) : $uri;
    $path = '/' . trim($path, '/');
    if ($path === '/index.php') $path = '/';
    foreach ($routes as [$m,$p,$h]) {
        if ($m === $_SERVER['REQUEST_METHOD'] && $p === $path) return $h;
    }
    return null;
}

// Register customer routes
require ROOT . '/routes/customer.php';
//Debug Url Path
/*echo "Method: $method <br>";
echo "URI: $uri <br>";
echo "Base: $base <br>";
echo "Path: $path <br>";
var_dump(route(null,null,null));
exit;*/

// Dispatch
$handler = route(null,null,null);
if ($handler) { $handler(); }
else { http_response_code(404); echo "404 Not Found "; }
