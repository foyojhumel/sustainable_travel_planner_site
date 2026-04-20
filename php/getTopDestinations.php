<?php

require_once 'dbConnect.php'; // pulls in the database connection from dbConnect.php
require_once __DIR__ . '/../config.php'; // pulls in the base URL from config.php

$sql = "SELECT destination, eco_indicator, rating, path, description
        FROM destination
        WHERE rating >= 4.0 AND path IS NOT NULL
        ORDER BY rating DESC";
$result = $conn->query($sql);

$destinations = array();
$baseurl = BASE_URL; // Use the base URL from config.php
while ($row = $result->fetch_assoc()) {
    // Prepend the base URL to the image path
    $row['path'] = $baseurl . $row['path'];
    $destinations[] = $row;
}

header('Content-Type: application/json');
echo json_encode($destinations);

$conn->close();

?>