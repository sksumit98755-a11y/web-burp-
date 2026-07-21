<?php
class ProxyHandler {
    private static $captured = [];
    private static $intercept = false;
    private static $storageFile = __DIR__ . '/../../storage/requests.json';

    public static function start($port) {
        self::loadCaptured();
        return ['status' => 'started', 'port' => $port];
    }

    public static function stop() {
        return ['status' => 'stopped'];
    }

    public static function toggleIntercept($enabled) {
        self::$intercept = $enabled;
        return ['intercept' => $enabled];
    }

    public static function getCaptured() {
        self::loadCaptured();
        return self::$captured;
    }

    private static function loadCaptured() {
        if (file_exists(self::$storageFile)) {
            $data = json_decode(file_get_contents(self::$storageFile), true);
            if (is_array($data)) {
                self::$captured = $data;
            }
        }
    }

    public static function captureRequest($request) {
        self::loadCaptured();
        if (self::$intercept) {
            $request['timestamp'] = date('Y-m-d H:i:s');
            self::$captured[] = $request;
            file_put_contents(self::$storageFile, json_encode(self::$captured, JSON_PRETTY_PRINT));
        }
        return $request;
    }
}
?>
