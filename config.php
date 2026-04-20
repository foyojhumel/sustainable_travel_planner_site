<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = str_replace('/php', '', $scriptPath);
define('BASE_URL', $protocol . '://' . $host . $projectRoot);