<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = strtolower(trim(filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL)));
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    // Redirects back to sign up page when there is any error
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        header("Location: " . BASE_URL . "/pages/sign_up.php");
        exit();
    }
    
    // Check if email already exists
    $checkSql = "SELECT user_id FROM users WHERE email = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        $errors[] = "Email is already registered. Please try another email.";
        $_SESSION['signup_errors'] = $errors;
        header("Location: " . BASE_URL . "/pages/sign_up.php");
        exit();
    }

    // If no errors, proceed with registration
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insertSql = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";
    $insertStmt = $pdo->prepare($insertSql);
    try {
        $insertStmt->execute([$name, $email, $hash]);
        $_SESSION['registration_success'] = "Registration successful! Please log in.";
        header("Location: " . BASE_URL . "/pages/log_in.php");
        exit();
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
        $_SESSION['signup_errors'] = $errors;
        header("Location: " . BASE_URL . "/pages/sign_up.php");
        exit();
    }
}

?>