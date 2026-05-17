<?php

class ApiHelpers {

    /**
     * Build a fully-qualified URL for a stored file. Handles every variant the
     * legacy code produces:
     *   - absolute URLs (returned as-is)
     *   - bare filenames                  ->  /CF-SYSTEMS/storage/invoice_files/<f>
     *   - "storage/foo/bar.jpg"           ->  /CF-SYSTEMS/storage/foo/bar.jpg
     *   - "CF-SYSTEMS/storage/foo.jpg"    ->  /CF-SYSTEMS/storage/foo.jpg
     *   - "/CF-SYSTEMS/storage/foo.jpg"   ->  /CF-SYSTEMS/storage/foo.jpg
     *   - "../../storage/foo/bar.jpg"     ->  /CF-SYSTEMS/storage/foo/bar.jpg
     */
    public static function normalizeUrl(?string $relative): ?string {
        $relative = trim((string)$relative);
        if ($relative === '') return null;

        if (preg_match('#^https?://#i', $relative)) return $relative;

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $scheme . '://' . $host;

        // Bare filename → invoice_files folder.
        if (strpos($relative, '/') === false) {
            return $base . '/CF-SYSTEMS/storage/invoice_files/' . rawurlencode($relative);
        }

        // Drop leading slashes and any number of "../" / "./" segments.
        $rel = ltrim($relative, '/');
        $rel = preg_replace('#^(?:\.\.?/)+#', '', $rel);

        // Ensure path is rooted at CF-SYSTEMS/storage. The legacy code emits
        // paths relative to a controller directory ("../../storage/...").
        if (strpos($rel, 'CF-SYSTEMS/') === 0) {
            // already absolute under the app root
        } elseif (strpos($rel, 'storage/') === 0) {
            $rel = 'CF-SYSTEMS/' . $rel;
        } else {
            // Unknown convention — keep as-is under the doc root but log once.
            // (Avoid silently losing the file path.)
        }
        return $base . '/' . $rel;
    }

    /** Returns a list of normalized variants of a phone number for matching. */
    public static function phoneVariants(string $raw): array {
        $n = preg_replace('/\D/', '', $raw);
        if ($n === '') return [];
        $out = [$n];
        if (strlen($n) >= 10) {
            $last10 = substr($n, -10);
            $out[] = $last10;
            $out[] = '1' . $last10;
        }
        if (strlen($n) >= 11) {
            $last11 = substr($n, -11);
            $out[] = $last11;
            if ($last11[0] === '1') $out[] = substr($last11, 1);
        }
        if (strlen($n) == 10) $out[] = '1' . $n;
        if (strlen($n) == 11 && $n[0] === '1') $out[] = substr($n, 1);
        return array_values(array_unique(array_filter($out)));
    }

    /** Find the first image filename associated with a car. */
    public static function carImageUrl(int $carId): ?string {
        $con = Database::getCon();
        if (!$con) return null;
        $carId = intval($carId);

        // 1. Gallery
        $r = @$con->query("SELECT invoice_file FROM galery WHERE car_id=$carId AND invoice_file<>'' ORDER BY id ASC LIMIT 1");
        if ($r && $row = $r->fetch_assoc()) {
            $f = trim((string)$row['invoice_file']);
            if ($f !== '') return self::normalizeUrl($f);
        }
        // 2. Cars.invoice_file
        $r = @$con->query("SELECT invoice_file FROM cars WHERE id=$carId LIMIT 1");
        if ($r && $row = $r->fetch_assoc()) {
            $f = trim((string)$row['invoice_file']);
            if ($f !== '') return self::normalizeUrl($f);
        }
        return null;
    }

    public static function carImages(int $carId): array {
        $con = Database::getCon();
        if (!$con) return [];
        $carId = intval($carId);
        $out = [];
        $r = @$con->query("SELECT invoice_file FROM galery WHERE car_id=$carId AND invoice_file<>'' ORDER BY id ASC");
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $u = self::normalizeUrl($row['invoice_file']);
                if ($u) $out[] = $u;
            }
        }
        if (!$out) {
            $primary = self::carImageUrl($carId);
            if ($primary) $out[] = $primary;
        }
        return $out;
    }

    /** Serialize a Cars row for API responses. */
    public static function carToArray($c): array {
        return [
            'id' => intval($c->id),
            'name' => (string)$c->name,
            'year' => (string)$c->year,
            'plate' => (string)$c->plate,
            'price' => floatval($c->price),
            'seat' => (string)$c->seat,
            'kms' => (string)$c->kms,
            'kms_current' => (string)$c->kms_current,
            'status' => intval($c->status),
            'brand_id' => intval($c->brand_id),
            'category_id' => intval($c->category_id),
            'transmission_id' => intval($c->transmission_id),
            'fuel_id' => intval($c->fuel_id),
            'stock_id' => intval($c->stock_id),
            'image' => self::carImageUrl(intval($c->id)),
            'images' => self::carImages(intval($c->id)),
        ];
    }

    public static function bookingToArray($b): array {
        return [
            'id' => intval($b->id),
            'code' => (string)$b->code,
            'status' => intval($b->status),
            'person_id' => intval($b->person_id),
            'car_id' => intval($b->car_id),
            'stock_id' => intval($b->stock_id),
            'start_at' => (string)$b->start_at,
            'end_at' => (string)$b->end_at,
            'place_start' => (string)$b->place_start,
            'place_end' => (string)$b->place_end,
            'day' => (string)$b->day,
            'price' => floatval($b->price),
            'total' => floatval($b->total),
            'payment' => floatval($b->payment),
            'fuel' => (string)$b->fuel,
            'comment' => (string)$b->comment,
            'created_at' => (string)$b->created_at,
        ];
    }

    public static function personToArray($p): array {
        return [
            'id' => intval($p->id),
            'name' => (string)$p->name,
            'lastname' => (string)$p->lastname,
            'email' => (string)$p->email,
            'phone' => (string)$p->phone,
            'address' => (string)$p->address,
            'nationality' => (string)$p->nationality,
            'passport' => (string)$p->passport,
            'license' => (string)$p->license,
            'stock_id' => intval($p->stock_id),
        ];
    }

    public static function userToArray($u): array {
        return [
            'id' => intval($u->id),
            'name' => (string)($u->name ?? ''),
            'lastname' => (string)($u->lastname ?? ''),
            'email' => (string)($u->email ?? ''),
            'phone' => (string)($u->phone ?? ''),
            'kind' => intval($u->kind ?? 0),
            'stock_id' => intval($u->stock_id ?? 0),
            'image' => !empty($u->image) ? self::normalizeUrl((string)$u->image) : null,
        ];
    }
}
