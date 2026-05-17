<?php

class ApiResponse {

    public static function ok($data = null, int $status = 200): void {
        self::send($status, ['ok' => true, 'data' => $data]);
    }

    public static function err(string $code, string $message, int $status = 400, $extra = null): void {
        $err = ['code' => $code, 'message' => $message];
        if ($extra !== null) $err['details'] = $extra;
        self::send($status, ['ok' => false, 'error' => $err]);
    }

    public static function send(int $status, array $body): void {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Parse JSON or form body into associative array. */
    public static function input(): array {
        $raw = file_get_contents('php://input');
        if ($raw !== '' && $raw !== false) {
            $j = json_decode($raw, true);
            if (is_array($j)) return $j;
        }
        if (!empty($_POST)) return $_POST;
        return [];
    }
}
