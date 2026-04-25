<?php

require_once 'dbConnect.php'; // pulls in the database connection from dbConnect.php
require_once __DIR__ . '/../config.php'; // pulls in the base URL from config.php

$sql = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating, d.path,
        d.description, l.location, l.location_id
        FROM destination d
        JOIN location l ON d.location_id = l.location_id
        WHERE d.rating >= 4.0 AND d.path IS NOT NULL
        ORDER BY d.rating DESC";

// Execute query using PDO
$stmt = $pdo->query($sql);
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$baseurl = BASE_URL;

// Process each destination
foreach ($destinations as &$row) {
    $row['destination_id'] = (int)$row['destination_id'];
    $row['location_id'] = (int)$row['location_id'];
    $row['path'] = $baseurl . $row['path'];
}
unset($row); // break reference

header('Content-Type: application/json');
echo json_encode($destinations);

?>