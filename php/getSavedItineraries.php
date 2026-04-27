<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT
            l.location_id,
            p.province,
            (SELECT d.destination FROM destination d WHERE d.location_id = l.location_id
                ORDER BY d.destination_id LIMIT 1) AS destination,
            (SELECT d.eco_indicator FROM destination d WHERE d.location_id = l.location_id
                ORDER BY d.destination_id LIMIT 1) AS eco_indicator,
            (SELECT d.rating FROM destination d WHERE d.location_id = l.location_id
                ORDER BY d.destination_id LIMIT 1) AS rating,
            (SELECT d.path FROM destination d WHERE d.location_id = l.location_id
                ORDER BY d.destination_id LIMIT 1) AS image_path
        FROM saved_itineraries si
        JOIN location l ON si.location_id = l.location_id
        JOIN province p ON l.province_id = p.province_id
        WHERE si.user_id = ?
        ORDER BY si.saved_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepend BASE_URL to image_path
foreach ($rows as &$row) {
    $row['image_url'] = BASE_URL . $row['image_path'];
}
unset($row);

echo json_encode($rows);

?>