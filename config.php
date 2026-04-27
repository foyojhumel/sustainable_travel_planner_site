<?php
// Absolute filesystem path of project
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = str_replace('/php', '', $scriptPath);
define('BASE_URL', $protocol . '://' . $host . $projectRoot);
define('PROJECT_ROOT_FS', $_SERVER['DOCUMENT_ROOT'] . $projectRoot);

// Profile image display helper
function getUserProfilePic() {
    if (!isset($_SESSION['profile_picture']) || empty($_SESSION['profile_picture'])) {
        $relativePath = 'images/profiles/default-avatar.jpg';
    } else {
        $relativePath = ltrim($_SESSION['profile_picture'], '/'); // remove leading slash if any
    }

    // Detect current script location
    $currentScript = $_SERVER['SCRIPT_NAME'];
    if (strpos($currentScript, '/pages/') !== false) {
        // Inside /pages/folder - need to go up one level
        return '../' . $relativePath;
    } else {
        // At root level, use relative path directly
        return $relativePath;
    }
}
?>