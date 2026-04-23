<?php

require_once 'dbConnect.php'; // pulls in the database connection from dbConnect.php
require_once __DIR__ . '/../config.php'; // pulls in the base URL from config.php

$sql = "SELECT d.destination_id, d.destination, d.eco_indicator, d.rating, d.path,
        d.description, l.location, l.location_id
        FROM destination d
        JOIN location l ON d.location_id = l.location_id
        WHERE d.rating >= 4.0 AND d.path IS NOT NULL
        ORDER BY d.rating DESC";
$result = $conn->query($sql);

$destinations = [];
$baseurl = BASE_URL; // Use the base URL from config.php
while ($row = $result->fetch_assoc()) {
    $row['destination_id'] = (int)$row['destination_id'];
    $row['location_id'] = (int)$row['location_id'];
    // Prepend the base URL to the image path
    $row['path'] = $baseurl . $row['path'];
    $destinations[] = $row;
}

header('Content-Type: application/json');
echo json_encode($destinations);

$conn->close();

?>