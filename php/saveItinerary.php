<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'dbConnect.php';

$user_id = $_SESSION['user_id'];
$location_id = isset($_POST['location_id']) ? (int)$_POST['location_id'] : 0;
if (!$location_id) {
    echo json_encode(['error' => 'Missing location_id']);
    exit;
}

// Check if already saved
$checkStmt = $pdo->prepare("SELECT id FROM saved_itineraries WHERE user_id = ? AND location_id = ?");
$checkStmt->execute([$user_id, $location_id]);
$exists = $checkStmt->fetch();

if ($exists) {
    // Unsave (delete) entry
    $delStmt = $pdo->prepare("DELETE FROM saved_itineraries WHERE user_id = ? AND location_id = ?");
    $delStmt-> execute([$user_id, $location_id]);
    echo json_encode(['saved' => false]);
} else {
    // Save (insert) entry
    $insStmt = $pdo->prepare("INSERT INTO saved_itineraries (user_id, location_id) VALUES (?, ?)");
    $insStmt->execute([$user_id, $location_id]);
    echo json_encode(['saved' => true]);
}

?>