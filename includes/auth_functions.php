<?php
require_once 'config.php';
require_once 'db_functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle login/logout actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'login':
            handle_login();
            break;
        case 'logout':
            handle_logout();
            break;
        case 'register':
            handle_register();
            break;
    }
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Handle login
function handle_login() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . SITE_URL . "/pages/login.php");
        exit;
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Get user data
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT id, password, locked_until, failed_attempts, first_name, last_name, profile_image, created_at FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if user is locked out
    if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $_SESSION['error'] = "Account locked. Please try again later.";
        header("Location: " . SITE_URL . "/pages/login.php");
        exit;
    }

    if ($user && password_verify($password, $user['password'])) {
        // Login successful - reset failed attempts
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $stmt->close();

        // Set session variables with user information
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_first_name'] = $user['first_name'];
        $_SESSION['user_last_name'] = $user['last_name'];
        $_SESSION['user_profile_image'] = $user['profile_image'];
        $_SESSION['user_created_at'] = $user['created_at'];
        $_SESSION['login_success'] = "You have successfully logged in!";
        
        header("Location: " . SITE_URL . "/pages/dashboard.php");
        exit;
    } else {
        // Login failed - increment failed attempts
        if ($user) {
            $new_attempts = $user['failed_attempts'] + 1;
            $locked_until = null;

            // Check if MAX_LOGIN_ATTEMPTS and LOCKOUT_TIME constants are defined
            $max_attempts = defined('MAX_LOGIN_ATTEMPTS') ? MAX_LOGIN_ATTEMPTS : 5;
            $lockout_time = defined('LOCKOUT_TIME') ? LOCKOUT_TIME : 1800; // 30 minutes default

            if ($new_attempts >= $max_attempts) {
                $locked_until = date('Y-m-d H:i:s', time() + $lockout_time);
                $_SESSION['error'] = "Too many failed attempts. Account locked for " . ($lockout_time / 60) . " minutes.";
            } else {
                $_SESSION['error'] = "Invalid email or password. " . ($max_attempts - $new_attempts) . " attempts remaining.";
            }

            $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
            $stmt->bind_param("isi", $new_attempts, $locked_until, $user['id']);
            $stmt->execute();
            $stmt->close();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
        }

        header("Location: " . SITE_URL . "/pages/login.php");
        exit;
    }
}

// Handle logout
function handle_logout() {
    session_unset();
    session_destroy();
    header("Location: " . SITE_URL);
    exit;
}

// Handle registration
// Handle registration
function handle_register() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . SITE_URL . "/pages/register.php");
        exit;
    }

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Check if this request came from the modal (has redirect_to parameter)
    $redirect_to_login = isset($_POST['redirect_to']) && $_POST['redirect_to'] === 'login';

    // Validate inputs
    $errors = [];

    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password)) $errors[] = "Password is required.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // Check if email exists
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Email already registered.";
    }
    $stmt->close();

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ];
        
        // If from modal, redirect to register page to show errors
        header("Location: " . SITE_URL . "/pages/register.php");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Create user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

    if ($stmt->execute()) {
        // Registration successful
        $_SESSION['registration_success'] = "Your account has been created successfully! You can now log in.";
        
        // Redirect to login page if request came from modal, otherwise stay on register page
        if ($redirect_to_login) {
            header("Location: " . SITE_URL . "/pages/login.php");
        } else {
            header("Location: " . SITE_URL . "/pages/register.php");
        }
    } else {
        // Registration failed
        $_SESSION['errors'] = ["Registration failed. Please try again."];
        $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ];
        header("Location: " . SITE_URL . "/pages/register.php");
    }

    $stmt->close();
    $conn->close();
    exit;
}
// Function to get user data (for use in other pages)
function get_user_data($user_id) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, profile_image, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $user;
}

// Function to update session with user data
function update_user_session($user_id) {
    $user_data = get_user_data($user_id);
    if ($user_data) {
        $_SESSION['user_first_name'] = $user_data['first_name'];
        $_SESSION['user_last_name'] = $user_data['last_name'];
        $_SESSION['user_email'] = $user_data['email'];
        $_SESSION['user_profile_image'] = $user_data['profile_image'];
        $_SESSION['user_created_at'] = $user_data['created_at'];
        return true;
    }
    return false;
}