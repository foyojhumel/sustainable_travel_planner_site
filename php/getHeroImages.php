<?php

require_once 'dbConnect.php'; // pulls in the database connection from dbConnect.php
require_once __DIR__ . '/../config.php'; // pulls in the base URL from config.php

$sql = "SELECT path FROM destination WHERE is_hero = 1";
$result = $conn->query($sql);

$images = array();
//$baseurl = "http://localhost/sustainable_travel_planner_site/"; // Actual base URL
$baseurl = BASE_URL; // Use the base URL from config.php
while ($row = $result->fetch_assoc()) {
    $images[] = $baseurl . $row['path'];
}

header('Content-Type: application/json');
echo json_encode($images);

$conn->close();

?>