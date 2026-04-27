<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['saved' => false]);
    exit;
}

require_once 'dbConnect.php';

$location_id = isset($_GET['location_id']) ? (int)$_GET['location_id'] : 0;
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM saved_itineraries WHERE user_id = ? AND location_id = ?");
$stmt->execute([$user_id, $location_id]);
$exists = $stmt->fetch();
echo json_encode(['saved' => (bool)$exists]);

?>