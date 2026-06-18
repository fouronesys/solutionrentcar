<?php

class ApiAuth {

    private static ?array $current = null;

    public static function bearerToken(): ?string {
        $hdr = '';
        if (function_exists('getallheaders')) {
            $h = getallheaders();
            foreach ($h as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $hdr = $v; break; }
            }
        }
        if ($hdr === '') {
            $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }
        if ($hdr === '') return null;
        if (preg_match('/Bearer\s+(.+)/i', $hdr, $m)) return trim($m[1]);
        return null;
    }

    public static function tryAuth(): ?array {
        if (self::$current !== null) return self::$current;
        $token = self::bearerToken();
        if (!$token) return null;
        $payload = Jwt::verify($token);
        if (!$payload || empty($payload['sub']) || empty($payload['typ'])) return null;
        $typ = (string)$payload['typ'];
        if ($typ !== 'user' && $typ !== 'client') return null;
        self::$current = [
            'id' => intval($payload['sub']),
            'type' => $typ,
            'stock_id' => intval($payload['stock_id'] ?? 0),
            'payload' => $payload,
        ];
        return self::$current;
    }

    public static function require(?string $type = null): array {
        $a = self::tryAuth();
        if (!$a) ApiResponse::err('unauthorized', 'Token requerido o inválido', 401);
        if ($type !== null && $a['type'] !== $type) {
            ApiResponse::err('forbidden', 'Acceso restringido', 403);
        }
        return $a;
    }

    public static function requireStaff(): array  { return self::require('user'); }
    public static function requireClient(): array { return self::require('client'); }

    /** Issue an access+refresh pair for a recipient. */
    public static function issueTokens(string $type, int $id, array $extra = []): array {
        $access = Jwt::sign(array_merge(['sub' => $id, 'typ' => $type], $extra), 3600);
        $refreshRaw = Jwt::randomToken();
        $hash = Jwt::hashRefresh($refreshRaw);
        $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30d

        $con = Database::getCon();
        $rt = $con->real_escape_string($type);
        $rid = intval($id);
        $h = $con->real_escape_string($hash);
        $e = $con->real_escape_string($expires);
        @$con->query("INSERT INTO refresh_token (recipient_type,recipient_id,token_hash,expires_at,created_at)
                      VALUES ('$rt',$rid,'$h','$e',NOW())");

        return [
            'access_token'  => $access,
            'refresh_token' => $refreshRaw,
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
        ];
    }

    /** Returns [type, recipient_id] if the refresh token is valid, null otherwise. */
    public static function consumeRefresh(string $refreshRaw): ?array {
        $con = Database::getCon();
        $hash = $con->real_escape_string(Jwt::hashRefresh($refreshRaw));
        $r = @$con->query("SELECT id, recipient_type, recipient_id, expires_at, revoked_at
                           FROM refresh_token WHERE token_hash='$hash' LIMIT 1");
        if (!$r || !($row = $r->fetch_assoc())) return null;
        if (!empty($row['revoked_at'])) return null;
        if (strtotime($row['expires_at']) <= time()) return null;
        // Atomic rotation — only one concurrent refresh wins.
        $id = intval($row['id']);
        @$con->query("UPDATE refresh_token SET revoked_at=NOW()
                      WHERE id=$id AND revoked_at IS NULL");
        if ($con->affected_rows !== 1) return null;
        return ['type' => $row['recipient_type'], 'id' => intval($row['recipient_id'])];
    }

    public static function revokeAllFor(string $type, int $id): void {
        $con = Database::getCon();
        $rt = $con->real_escape_string($type);
        $rid = intval($id);
        @$con->query("UPDATE refresh_token SET revoked_at=NOW()
                      WHERE recipient_type='$rt' AND recipient_id=$rid AND revoked_at IS NULL");
    }
}
