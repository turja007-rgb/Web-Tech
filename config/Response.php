<?php
// config/Response.php
namespace Config;

class Response {
    public static function view(string $path, array $data = []) {
        extract($data, EXTR_SKIP);
        include $path; // path to .php view
    }

    public static function redirect(string $to) {
        header("Location: {$to}");
        exit;
    }

    public static function setFlash(string $key, $val): void {
        $_SESSION['flash'][$key] = $val;
    }

    public static function getFlash(string $key, $default = null) {
        if (!empty($_SESSION['flash'][$key])) {
            $v = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $v;
        }
        return $default;
    }
}
