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
    // Default avatar
    $default = '/images/profiles/default-avatar.jpg';
    if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
        return '..' . $_SESSION['profile_picture'];
    }
    return $default;
}

// Profile image display helper for index.php
function getUserProfilePicIndex() {
    // Default avatar
    $default = '/images/profiles/default-avatar.jpg';
    if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
        return '.' . $_SESSION['profile_picture'];
    }
    return $default;
}

?>