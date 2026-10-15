<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'foodfusion');

// Site configuration
define('SITE_NAME', 'FoodFusion');
define('SITE_URL', 'http://localhost/foodfusion');
define('SITE_EMAIL', 'info@foodfusion.com');

// Security settings
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_TIME', 180); // 3 minutes in seconds

// Start session
session_start();

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
require_once 'db_functions.php';
require_once 'auth_functions.php';
?>