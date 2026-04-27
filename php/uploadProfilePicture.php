<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['error' => 'Only JPG, JPEG, PNG images are allowed.']);
        exit;
    }
    if ($file['size'] > $maxSize) {
        echo json_encode(['error' => 'File size must be less than 2 MB.']);
        exit;
    }

    // Upload directory inside project root
    $uploadDir = PROJECT_ROOT_FS . '/images/profiles/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $user_id . '_' . time() . '_' . $ext;
    $destination = $uploadDir . $filename;
    $relativePath = '/images/profiles/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Delete old profile picture
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $oldPic = $stmt->fetchColumn();
        if ($oldPic) {
            $oldPath = PROJECT_ROOT_FS . '/' . $oldPic;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Update database
        $updateStmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $updateStmt->execute([$relativePath, $user_id]);

        // Also update session variable
        $_SESSION['profile_picture'] = $relativePath; // stores relative path

        echo json_encode(['success' => true, 'new_url' => BASE_URL . $relativePath]);
    } else {
        echo json_encode(['error' => 'Failed to save file.']);
    }
    exit;
} else {
    echo json_encode(['error' => 'Invalid request']);
}

?>