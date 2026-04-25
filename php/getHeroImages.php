<?php

require_once 'dbConnect.php'; // pulls in the database connection from dbConnect.php
require_once __DIR__ . '/../config.php'; // pulls in the base URL from config.php

$sql = "SELECT path FROM destination WHERE is_hero = 1";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$images = [];
$baseurl = BASE_URL;

foreach ($results as &$row) {
    $images[] = $baseurl . $row['path'];
}
unset($row);

header('Content-Type: application/json');
echo json_encode($images);

?>