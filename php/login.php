<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // If not POST, redirect to login page
    header("Location: " . BASE_URL . "/pages/log_in.php");
    exit();
}

// Get POST data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($email)) $errors[] = "Email is required.";
if (empty($password)) $errors[] = "Password is required.";

if (empty($errors)) {
    $sql = "SELECT user_id, name, password_hash FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            // Login successful
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            // Redirect to profile page
            header("Location: " . BASE_URL . "/pages/profile.html");
            exit();
        } else {
            $errors[] = "Invalid email or password.";
        }
    } else {
        $errors[] = "Invalid email or password.";
    }
    $stmt->close();
}

// If errors, show error and redirect back to login page
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header("Location: " . BASE_URL . "/pages/log_in.php");
    exit();
}

$conn->close();

?>