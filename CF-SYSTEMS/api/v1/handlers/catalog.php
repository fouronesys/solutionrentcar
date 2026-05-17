<?php
/**
 * GET /catalog/{kind}  — read-only auxiliary catalogs.
 * Supported kinds: brands, categories, transmissions, fuels, colors, locations, stocks, insurances
 */

if ($method !== 'GET') ApiResponse::err('method_not_allowed', 'Use GET', 405);
ApiAuth::require();

$kind = strtolower($segments[1] ?? '');
$map = [
    'brands'        => 'brand',
    'categories'    => 'category',
    'transmissions' => 'transmission',
    'fuels'         => 'fuel',
    'colors'        => 'color',
    'locations'     => 'location',
    'stocks'        => 'stock',
    'insurances'    => 'insurance',
];

if (!isset($map[$kind])) ApiResponse::err('not_found', "Catálogo '$kind' no soportado", 404);

$table = $map[$kind];
$con = Database::getCon();
$r = @$con->query("SELECT * FROM $table ORDER BY id ASC");
$rows = [];
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $rows[] = [
            'id'   => intval($row['id']),
            'name' => (string)($row['name'] ?? ''),
        ];
    }
}
ApiResponse::ok([$kind => $rows]);
