<?php
/**
 * GET /places/search?q=...&lang=es      place autocomplete (proxied Nominatim)
 * GET /places/reverse?lat=..&lon=..&lang=es   reverse geocoding
 *
 * Proxies OpenStreetMap Nominatim server-side so the mobile apps never hit
 * the public geocoder directly (its usage policy forbids client-side
 * autocomplete). Adds an identifying User-Agent, a small file cache, and a
 * shared 1 req/s throttle.
 */

if ($method !== 'GET') ApiResponse::err('method_not_allowed', 'Use GET', 405);

$sub = strtolower($segments[1] ?? '');
if ($sub !== 'search' && $sub !== 'reverse') {
    ApiResponse::err('not_found', 'Endpoint no encontrado', 404);
}

$lang = (($_GET['lang'] ?? 'es') === 'en') ? 'en' : 'es';

$cacheDir = sys_get_temp_dir() . '/places_cache';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0777, true);

if ($sub === 'search') {
    $q = trim((string)($_GET['q'] ?? ''));
    if (mb_strlen($q) < 3) ApiResponse::ok(['results' => []]);
    if (mb_strlen($q) > 120) $q = mb_substr($q, 0, 120);
    $url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=0'
         . '&limit=6&countrycodes=do&accept-language=' . $lang
         . '&q=' . rawurlencode($q);
    $cacheKey = 's_' . $lang . '_' . md5(mb_strtolower($q));
    $ttl = 86400; // 24h — place names change rarely
} else {
    $lat = (float)($_GET['lat'] ?? 0);
    $lon = (float)($_GET['lon'] ?? 0);
    if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180 || ($lat === 0.0 && $lon === 0.0)) {
        ApiResponse::err('invalid_request', 'lat/lon inválidos', 400);
    }
    // Round to ~100 m so nearby lookups share cache entries
    $latR = round($lat, 3);
    $lonR = round($lon, 3);
    $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2'
         . '&accept-language=' . $lang . "&lat=$latR&lon=$lonR";
    $cacheKey = 'r_' . $lang . '_' . md5("$latR,$lonR");
    $ttl = 86400;
}

$cacheFile = "$cacheDir/$cacheKey.json";
if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
    $raw = (string)file_get_contents($cacheFile);
} else {
    // Shared throttle: ensure at least 1 s between upstream requests.
    $lockFile = "$cacheDir/throttle.lock";
    $lock = fopen($lockFile, 'c');
    if ($lock) {
        flock($lock, LOCK_EX);
        $last = (float)@file_get_contents($lockFile);
        $wait = 1.0 - (microtime(true) - $last);
        if ($wait > 0 && $wait <= 1.0) usleep((int)($wait * 1000000));
        @file_put_contents($lockFile, (string)microtime(true));
        flock($lock, LOCK_UN);
        fclose($lock);
    }

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'timeout' => 6,
            'header'  => "User-Agent: YowellRentCar/1.0 (booking app; contact via app)\r\nAccept: application/json\r\n",
        ],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        // Serve stale cache on upstream failure, if any
        if (is_file($cacheFile)) {
            $raw = (string)file_get_contents($cacheFile);
        } else {
            ApiResponse::err('upstream_error', 'Servicio de lugares no disponible', 502);
        }
    } else {
        @file_put_contents($cacheFile, $raw);
    }
}

$data = json_decode($raw, true);

function place_row($r) {
    $display = (string)($r['display_name'] ?? '');
    $parts = array_values(array_filter(array_map('trim', explode(',', $display))));
    $title = (string)($r['name'] ?? '') !== '' ? (string)$r['name'] : ($parts[0] ?? $display);
    $context = array_slice(array_values(array_filter(
        array_slice($parts, 1),
        function ($p) { return !preg_match('/^\d{4,6}$/', $p); }
    )), 0, 3);
    return [
        'id'       => (string)($r['place_id'] ?? (($r['lat'] ?? '') . ',' . ($r['lon'] ?? ''))),
        'title'    => $title,
        'subtitle' => implode(', ', $context),
        'label'    => implode(', ', array_merge([$title], array_slice($context, 0, 2))),
        'lat'      => (float)($r['lat'] ?? 0),
        'lng'      => (float)($r['lon'] ?? 0),
    ];
}

if ($sub === 'search') {
    $results = [];
    if (is_array($data)) {
        foreach ($data as $r) {
            if (is_array($r)) $results[] = place_row($r);
        }
    }
    ApiResponse::ok(['results' => $results]);
} else {
    if (!is_array($data) || empty($data['display_name'])) {
        ApiResponse::ok(['result' => null]);
    }
    ApiResponse::ok(['result' => place_row($data)]);
}
