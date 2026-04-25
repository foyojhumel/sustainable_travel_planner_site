<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../php/dbConnect.php';

$user_id = $_SESSION['user_id'];
$motto = trim($_POST['motto'] ?? '');

// Allow empty motto (set to NULL in database)
if ($motto === '') {
    $sql = "UPDATE users SET motto = NULL WHERE user_id = ?";
} else {
    $sql = "UPDATE users SET motto = ? WHERE user_id = ?";
}

$stmt = $pdo->prepare($sql);
if ($motto === '') {
    $stmt->execute([$user_id]);
} else {
    $stmt->execute([$motto, $user_id]);
}

echo json_encode(['success' => true]);

?>