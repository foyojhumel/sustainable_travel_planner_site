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
$stmtLoc = $conn->prepare($sqlLocation);
$stmtLoc->bind_param("i", $location_id);
$stmtLoc->execute();
$locationResult = $stmtLoc->get_result();
$location = $locationResult->fetch_assoc();

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
$stmtDests = $conn->prepare($sqlDestinations);
$stmtDests->bind_param("i", $location_id);
$stmtDests->execute();
$destinationsResult = $stmtDests->get_result();
$destinations = [];
$baseurl = BASE_URL; // Use the base URL from config.php
while ($row = $destinationsResult->fetch_assoc()) {
    // Prepend the base URL to the image path
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

$conn->close();

?>