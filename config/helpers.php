<?php
if (!function_exists('url')) {
    function url(string $path = ''): string {
        $cfg = require ROOT . '/config/config.php';
        return rtrim($cfg['app_url'], '/') . $path;
    }
}
if (!function_exists('displayFlash')) {
    function displayFlash(array $flashVar, string $flashKey, string $status = 'danger'): void
    {
        $message = isset($flashVar[$flashKey]) ? $flashVar[$flashKey] : null;

        if (!empty($message)) {
            $message = json_encode($message, ENT_QUOTES);
            $status = json_encode($status, ENT_QUOTES);
            echo <<<SCRIPT
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    if (typeof flashToast === "function") {
                        showToast({$message}, {$status});
                    } else {
                        console.error("flashToast function not defined");
                    }
                });
                </script>
                SCRIPT;

        }

    }
}



/*if (!function_exists('url')) {
    function url(string $path = ''): string {
        $cfg = require __DIR__ . '/config.php';
        return rtrim($cfg['app_url'], '/') . '/' . ltrim($path, '/');
    }
}*/


