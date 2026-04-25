<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'dbConnect.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "/pages/log_in.php");
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$errors = [];
if (empty($email)) $errors[] = "Email is required.";
if (empty($password)) $errors[] = "Password is required.";

if (empty($errors)) {
    $sql = "SELECT user_id, name, password_hash FROM users WHERE email = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: " . BASE_URL . "/pages/profile.php");
            exit();
        } else {
            $errors[] = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $errors[] = "System error. Please try again.";
        error_log("Login error: " . $e->getMessage());
    }
}

$_SESSION['login_errors'] = $errors;
header("Location: " . BASE_URL . "/pages/log_in.php");
exit();

?>