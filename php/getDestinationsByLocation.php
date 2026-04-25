<?php

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

// Get location_id from query string
$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;
if (!$location_id) {
    echo json_encode(['error' => 'Missing or invalid location_id']);
    exit;
}

// 1. Get location info (location name and description)
$sqlLocation = "SELECT location, description FROM location WHERE location_id = ?";
$stmtLoc = $pdo->prepare($sqlLocation);
$stmtLoc->execute([$location_id]);
$location = $stmtLoc->fetch(PDO::FETCH_ASSOC);

if (!$location) {
    echo json_encode(['error' => 'Location not found']);
    exit;
}

// 2. Get all destinations in this location
$sqlDestinations = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating,
                d.path, d.description,
                CONCAT(d.destination, ', ', l.location, ', ', p.province, ', Philippines') AS address
                FROM destination d
                JOIN location l ON d.location_id = l.location_id
                JOIN province p ON l.province_id = p.province_id
                WHERE d.location_id = ? AND path IS NOT NULL";
$stmtDests = $pdo->prepare($sqlDestinations);
$stmtDests->execute([$location_id]);
$destinationsResult = $stmtDests->fetchAll(PDO::FETCH_ASSOC);
$destinations = [];
$baseurl = BASE_URL;
foreach ($destinationsResult as $row) {
    $row['path'] = $baseurl . $row['path'];
    $destinations[] = $row;
}

// Combine location info and destinations into a single response
$response = [
    'location' => [
        'name' => $location['location'],
        'description' => $location['description']
    ],
    'destinations' => $destinations
];

header('Content-Type: application/json');
echo json_encode($response);

?>