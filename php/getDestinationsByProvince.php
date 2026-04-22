<?php

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

// Get province_id from query string
$province_id = isset($_GET['province_id']) ? intval($_GET['province_id']) : 0;
if (!$province_id) {
    echo json_encode(['error' => 'Missing or invalid province_id']);
    exit;
}

// 1. Provide info (province name and description)
$sqlProvince = "SELECT province, description FROM province WHERE province_id = ?";
$stmtProv = $conn->prepare($sqlProvince);
$stmtProv->bind_param("i", $province_id);
$stmtProv->execute();
$provinceResult = $stmtProv->get_result();
$province = $provinceResult->fetch_assoc();

if (!$province) {
    echo json_encode(['error' => 'Province not found']);
    exit;
}

// 2. Top destinations (high rating of >= 4.0, limit 6)
$sqlTop = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating,
            d.path, d.description, l.location, l.location_id
            FROM destination d
            JOIN location l ON d.location_id = l.location_id
            WHERE l.province_id = ? AND d.rating >= 4.0
            ORDER BY d.rating DESC
            LIMIT 6";
$stmtTop = $conn->prepare($sqlTop);
$stmtTop->bind_param("i", $province_id);
$stmtTop->execute();
$topResult = $stmtTop->get_result();
$topDestinations = [];
$baseurl = BASE_URL; // Use the base URL from config.php
while ($row = $topResult->fetch_assoc()) {
    // Prepend the base URL to the image path
    $row['path'] = $baseurl . $row['path'];
    $topDestinations[] = $row;
}

// 3. Off Beaten Path (lower rating of < 4.0, limit 6)
$sqlOff = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating,
        d.path, d.description, l.location, l.location_id
        FROM destination d
        JOIN location l ON d.location_id = l.location_id
        WHERE l.province_id = ? AND d.rating < 4.0
        ORDER BY d.rating DESC";
$stmtOff = $conn->prepare($sqlOff);
$stmtOff->bind_param("i", $province_id);
$stmtOff->execute();
$offResult = $stmtOff->get_result();
$offDestinations = [];
$baseurl = BASE_URL; // Use the base URL from config.php
while ($row = $offResult->fetch_assoc()) {
    // Prepend the base URL to the image path
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

$conn->close();

?>