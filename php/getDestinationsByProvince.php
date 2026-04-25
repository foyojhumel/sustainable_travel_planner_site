<?php

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

// Get province_id from query string
$province_id = isset($_GET['province_id']) ? intval($_GET['province_id']) : 0;
if (!$province_id) {
    //header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing or invalid province_id']);
    exit;
}

// 1. Provide info (province name and description)
$sqlProvince = "SELECT province, description FROM province WHERE province_id = ?";
$stmtProv = $pdo->prepare($sqlProvince);
$stmtProv->execute([$province_id]);
$province = $stmtProv->fetch(PDO::FETCH_ASSOC);

if (!$province) {
    echo json_encode(['error' => 'Province not found']);
    exit;
}

// 2. Top destinations (high rating of >= 4.0, limit 6)
$sqlTop = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating,
            d.path, d.description, l.location, l.location_id
            FROM destination d
            JOIN location l ON d.location_id = l.location_id
            WHERE l.province_id = ? AND d.rating >= 4.0 AND d.path IS NOT NULL
            ORDER BY d.rating DESC
            LIMIT 6";
$stmtTop = $pdo->prepare($sqlTop);
$stmtTop->execute([$province_id]);
$topResult = $stmtTop->fetchAll(PDO::FETCH_ASSOC);
$topDestinations = [];
$baseurl = BASE_URL;
foreach ($topResult as $row) {
    $row['path'] = $baseurl . $row['path'];
    $topDestinations[] = $row;
}

// 3. Off Beaten Path (lower rating of < 4.0, limit 6)
$sqlOff = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating,
        d.path, d.description, l.location, l.location_id
        FROM destination d
        JOIN location l ON d.location_id = l.location_id
        WHERE l.province_id = ? AND d.rating < 4.0 AND d.path IS NOT NULL
        ORDER BY d.rating DESC";
$stmtOff = $pdo->prepare($sqlOff);
$stmtOff->execute([$province_id]);
$offResult = $stmtOff->fetchAll(PDO::FETCH_ASSOC);
$offDestinations = [];
$baseurl = BASE_URL;
foreach ($offResult as $row) {
    $row['path'] = $baseurl . $row['path'];
    $offDestinations[] = $row;
}

// Build final JSON response
$response = [
    'province' => [
        'name' => $province['province'],
        'description' => $province['description']
    ],
    'top_destinations' => $topDestinations,
    'off_beaten_path' => $offDestinations
];

header('Content-Type: application/json');
echo json_encode($response);

?>