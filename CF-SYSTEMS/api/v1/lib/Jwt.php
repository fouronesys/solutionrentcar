<?php
/**
 * Minimal JWT (HS256) implementation — no Composer dependency.
 */
class Jwt {

    public static function secret(): string {
        $env = getenv('JWT_SECRET');
        if ($env && strlen($env) >= 32) return $env;

        // Per-install persistent secret. Generated on first call and stored in
        // CF-SYSTEMS/storage/.jwt_secret (mode 0600). NEVER use a hardcoded
        // fallback — that would make tokens forgeable.
        $path = dirname(__DIR__, 3) . '/storage/.jwt_secret';
        if (is_readable($path)) {
            $s = trim((string)@file_get_contents($path));
            if (strlen($s) >= 32) return $s;
        }
        $dir = dirname($path);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $new = bin2hex(random_bytes(32));
        if (@file_put_contents($path, $new, LOCK_EX) === false) {
            // As an absolute last resort (read-only FS), use a process-stable
            // secret. This token won't validate across requests, forcing
            // re-login but never allowing forgery.
            static $mem = null;
            if ($mem === null) $mem = bin2hex(random_bytes(32));
            error_log('[Jwt] Could not persist secret to '.$path.' — tokens will not survive process restart.');
            return $mem;
        }
        @chmod($path, 0600);
        return $new;
    }

    private static function b64u_encode(string $bin): string {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    private static function b64u_decode(string $s): string {
        $pad = strlen($s) % 4;
        if ($pad) $s .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($s, '-_', '+/')) ?: '';
    }

    /**
     * @param array  $payload  ['sub'=>id,'type'=>'user|client',...]
     * @param int    $ttlSec   lifetime in seconds (default 1h)
     */
    public static function sign(array $payload, int $ttlSec = 3600): string {
        $now = time();
        $payload = array_merge([
            'iat' => $now,
            'exp' => $now + $ttlSec,
        ], $payload);

        $header  = ['alg' => 'HS256', 'typ' => 'JWT'];
        $h64 = self::b64u_encode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $p64 = self::b64u_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $sig = hash_hmac('sha256', "$h64.$p64", self::secret(), true);
        $s64 = self::b64u_encode($sig);
        return "$h64.$p64.$s64";
    }

    /** @return array|null Decoded payload or null on failure. */
    public static function verify(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$h64, $p64, $s64] = $parts;
        $expected = self::b64u_encode(hash_hmac('sha256', "$h64.$p64", self::secret(), true));
        if (!hash_equals($expected, $s64)) return null;
        $payload = json_decode(self::b64u_decode($p64), true);
        if (!is_array($payload)) return null;
        if (isset($payload['exp']) && time() >= intval($payload['exp'])) return null;
        return $payload;
    }

    public static function randomToken(int $bytes = 48): string {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }

    public static function hashRefresh(string $token): string {
        return hash('sha256', $token);
    }
}
