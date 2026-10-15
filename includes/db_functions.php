<?php
require_once 'config.php';
// session_start();

// Create database connection
function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// General query function
function db_query($sql, $params = []) {
    $conn = get_db_connection();
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false && $stmt->errno) {
        die("Error executing query: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Get featured recipes
function get_featured_recipes($limit = 6) {
    $sql = "SELECT r.*, u.first_name, u.last_name 
            FROM recipes r
            LEFT JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
            LIMIT ?";
    $result = db_query($sql, [$limit]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all recipes with pagination
function get_all_recipes($page = 1, $per_page = 12) {
    $offset = ($page - 1) * $per_page;
    $sql = "SELECT r.*, u.first_name, u.last_name 
            FROM recipes r
            LEFT JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
            LIMIT ?, ?";
    $result = db_query($sql, [$offset, $per_page]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Count total recipes
function count_recipes() {
    $sql = "SELECT COUNT(*) as total FROM recipes";
    $result = db_query($sql);
    return $result->fetch_assoc()['total'];
}

// Get upcoming events
function get_upcoming_events($limit = 6) {
    $sql = "SELECT * FROM events 
            ORDER BY event_date ASC
            LIMIT ?";
    $result = db_query($sql, [$limit]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to format date for display
function formatEventDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M j');
}

// Function to format time for display
function formatEventTime($dateString) {
    $date = new DateTime($dateString);
    return $date->format('g:i a');
}

// Get recipe by ID
function get_recipe_by_id($id) {
    $sql = "SELECT r.*, u.first_name, u.last_name, u.profile_image
            FROM recipes r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.id = ?";
    $result = db_query($sql, [$id]);
    return $result->fetch_assoc();
}

// Get recipe categories
function get_recipe_categories($recipe_id) {
    $sql = "SELECT c.id, c.name 
            FROM categories c
            JOIN recipe_categories rc ON c.id = rc.category_id
            WHERE rc.recipe_id = ?";
    $result = db_query($sql, [$recipe_id]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all categories
function get_all_categories() {
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = db_query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Search recipes
function search_recipes($query, $limit = 10) {
    $search_query = "%$query%";
    $sql = "SELECT r.*, u.first_name, u.last_name
            FROM recipes r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?
            LIMIT ?";
    $result = db_query($sql, [$search_query, $search_query, $search_query, $limit]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Add new recipe
function add_recipe($recipe_data) {
    $sql = "INSERT INTO recipes (
            user_id, title, description, ingredients, instructions, 
            prep_time, cook_time, servings, difficulty, cuisine_type, 
            dietary_preferences, image_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $recipe_data['user_id'],
        $recipe_data['title'],
        $recipe_data['description'],
        $recipe_data['ingredients'],
        $recipe_data['instructions'],
        $recipe_data['prep_time'],
        $recipe_data['cook_time'],
        $recipe_data['servings'],
        $recipe_data['difficulty'],
        $recipe_data['cuisine_type'],
        $recipe_data['dietary_preferences'],
        $recipe_data['image_path']
    ];
    
    try {
        $conn = get_db_connection();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("issssiiissss", ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
        
        $recipe_id = $stmt->insert_id;
        $stmt->close();
        $conn->close();
        
        return $recipe_id;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Update recipe
function update_recipe($recipe_id, $recipe_data) {
    $sql = "UPDATE recipes SET
            title = ?,
            description = ?,
            ingredients = ?,
            instructions = ?,
            prep_time = ?,
            cook_time = ?,
            servings = ?,
            difficulty = ?,
            cuisine_type = ?,
            dietary_preferences = ?,
            image_path = ?
        WHERE id = ? AND user_id = ?";
    
    $params = [
        $recipe_data['title'],
        $recipe_data['description'],
        $recipe_data['ingredients'],
        $recipe_data['instructions'],
        $recipe_data['prep_time'],
        $recipe_data['cook_time'],
        $recipe_data['servings'],
        $recipe_data['difficulty'],
        $recipe_data['cuisine_type'],
        $recipe_data['dietary_preferences'],
        $recipe_data['image_path'],
        $recipe_id,
        $recipe_data['user_id']
    ];
    
    try {
        $conn = get_db_connection();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("ssssiiissssii", ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
        
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        
        return $affected_rows > 0;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Delete recipe
function delete_recipe($recipe_id, $user_id) {
    try {
        $conn = get_db_connection();
        
        // First delete from recipe_categories
        $sql = "DELETE FROM recipe_categories WHERE recipe_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->close();
        
        // Then delete the recipe
        $sql = "DELETE FROM recipes WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $recipe_id, $user_id);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        
        return $affected_rows > 0;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Update recipe categories
function update_recipe_categories($recipe_id, $category_ids) {
    // First delete existing categories
    $sql = "DELETE FROM recipe_categories WHERE recipe_id = ?";
    db_query($sql, [$recipe_id]);
    
    // Add new categories
    if (!empty($category_ids)) {
        $sql = "INSERT INTO recipe_categories (recipe_id, category_id) VALUES ";
        $placeholders = [];
        $params = [];
        
        foreach ($category_ids as $cat_id) {
            $placeholders[] = "(?, ?)";
            $params[] = $recipe_id;
            $params[] = $cat_id;
        }
        
        $sql .= implode(", ", $placeholders);
        db_query($sql, $params);
    }
}

// Get user recipes
function get_user_recipes($user_id) {
    $sql = "SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC";
    $result = db_query($sql, [$user_id]);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle file upload
function handle_file_upload($field_name, $target_dir) {
    if (!isset($_FILES[$field_name])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    $file = $_FILES[$field_name];
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error: ' . $file['error']];
    }
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Only JPG, PNG, and GIF images are allowed'];
    }
    
    // Check file size (max 2MB)
    $max_size = 2 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size must be less than 2MB'];
    }
    
    // Generate unique filename
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_ext;
    $target_path = $target_dir . $file_name;
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'file_name' => $file_name];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
}

// Handle form submissions
function handle_requests() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_recipe' || $action === 'update_recipe') {
            // Verify user is logged in
            if (!isset($_SESSION['user_id'])) {
                header("Location: " . SITE_URL . "/pages/login.php");
                exit;
            }
            
            $recipe_id = $_POST['recipe_id'] ?? 0;
            $user_id = $_SESSION['user_id'];
            
            // Handle file upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = handle_file_upload('image', '../assets/images/recipes/');
                if ($upload_result['success']) {
                    $image_path = $upload_result['file_name'];
                } else {
                    header("Location: " . SITE_URL . "/pages/recipes.php?action=" . ($action === 'add_recipe' ? 'add' : 'edit&id=' . $recipe_id) . "&error=upload");
                    exit;
                }
            } elseif ($action === 'update_recipe' && isset($_POST['remove_image']) && $_POST['remove_image'] === 'on') {
                $image_path = ''; // Remove image
            } elseif ($action === 'update_recipe' && $recipe_id) {
                // Keep existing image if not changing
                $existing_recipe = get_recipe_by_id($recipe_id);
                $image_path = $existing_recipe['image_path'];
            }
            
            // Prepare recipe data
            $recipe_data = [
                'user_id' => $user_id,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'ingredients' => $_POST['ingredients'],
                'instructions' => $_POST['instructions'],
                'prep_time' => $_POST['prep_time'],
                'cook_time' => $_POST['cook_time'],
                'servings' => $_POST['servings'],
                'difficulty' => $_POST['difficulty'],
                'cuisine_type' => $_POST['cuisine_type'],
                'dietary_preferences' => $_POST['dietary_preferences'],
                'image_path' => $image_path
            ];
            
            // Add or update recipe
            if ($action === 'add_recipe') {
                $recipe_id = add_recipe($recipe_data);
                $success = $recipe_id !== false;
            } else {
                $success = update_recipe($recipe_id, $recipe_data);
            }
            
            // Handle categories if operation was successful
            if ($success && isset($_POST['categories'])) {
                update_recipe_categories($recipe_id, $_POST['categories']);
            }
            
            // Redirect based on result
            if ($success) {
                header("Location: " . SITE_URL . "/pages/recipes.php?action=view&id=" . $recipe_id);
                exit;
            } else {
                header("Location: " . SITE_URL . "/pages/recipes.php?action=" . ($action === 'add_recipe' ? 'add' : 'edit&id=' . $recipe_id) . "&error=db");
                exit;
            }
        } elseif ($action === 'delete_recipe') {
            // Handle recipe deletion
            if (!isset($_SESSION['user_id'])) {
                header('HTTP/1.1 401 Unauthorized');
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $recipe_id = $_POST['id'] ?? 0;
            $user_id = $_SESSION['user_id'];
            
            $success = delete_recipe($recipe_id, $user_id);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'message' => $success ? 'Recipe deleted' : 'Failed to delete recipe']);
            exit;
        }
    }
}
// Get all resources with optional filtering by type
function get_all_resources($type = null) {
    $sql = "SELECT * FROM resources";
    $params = [];
    
    if ($type) {
        $sql .= " WHERE resource_type = ?";
        $params[] = $type;
    }
    
    $sql .= " ORDER BY created_at DESC";
    $result = db_query($sql, $params);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get resource by ID
function get_resource_by_id($id) {
    $sql = "SELECT * FROM resources WHERE id = ?";
    $result = db_query($sql, [$id]);
    return $result->fetch_assoc();
}

// Add new resource
function add_resource($resource_data) {
    $sql = "INSERT INTO resources (title, description, resource_type, file_path) VALUES (?, ?, ?, ?)";
    $params = [
        $resource_data['title'],
        $resource_data['description'],
        $resource_data['resource_type'],
        $resource_data['file_path']
    ];
    
    $conn = get_db_connection();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", ...$params);
    $stmt->execute();
    $resource_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    
    return $resource_id;
}
// Add these functions to your db_functions.php file

function get_community_posts($limit = 10, $offset = 0) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("
        SELECT cp.*, u.first_name, u.last_name, u.profile_image, 
               COUNT(DISTINCT pl.id) as like_count,
               COUNT(DISTINCT pc.id) as comment_count
        FROM community_posts cp
        JOIN users u ON cp.user_id = u.id
        LEFT JOIN post_likes pl ON cp.id = pl.post_id
        LEFT JOIN post_comments pc ON cp.id = pc.post_id
        GROUP BY cp.id
        ORDER BY cp.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $posts;
}

function create_community_post($user_id, $title, $content, $image_path = null, $post_type = 'recipe', $recipe_id = null) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO community_posts (user_id, title, content, image_path, post_type, recipe_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user_id, $title, $content, $image_path, $post_type, $recipe_id);
    $result = $stmt->execute();
    $post_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $result ? $post_id : false;
}

function add_post_comment($post_id, $user_id, $content) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $content);
    $result = $stmt->execute();
    $comment_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $result ? $comment_id : false;
}

function toggle_post_like($post_id, $user_id) {
    $conn = get_db_connection();
    
    // Check if already liked
    $stmt = $conn->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Unlike
        $stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return ['action' => 'unlike', 'success' => $result];
    } else {
        // Like
        $stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $post_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return ['action' => 'like', 'success' => $result];
    }
}

function get_post_comments($post_id) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("
        SELECT pc.*, u.first_name, u.last_name, u.profile_image
        FROM post_comments pc
        JOIN users u ON pc.user_id = u.id
        WHERE pc.post_id = ?
        ORDER BY pc.created_at ASC
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $comments;
}

function get_community_stats() {
    $conn = get_db_connection();
    
    // Total members
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $total_members = $result->fetch_assoc()['count'];
    
    // Recipes shared
    $result = $conn->query("SELECT COUNT(*) as count FROM community_posts WHERE post_type = 'recipe'");
    $recipes_shared = $result->fetch_assoc()['count'];
    
    // Active discussions (posts with comments in last 7 days)
    $result = $conn->query("SELECT COUNT(DISTINCT cp.id) as count 
                           FROM community_posts cp 
                           JOIN post_comments pc ON cp.id = pc.post_id 
                           WHERE pc.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $active_discussions = $result->fetch_assoc()['count'];
    
    // Events this month (placeholder - you might want to create an events table)
    $events_this_month = 4;
    
    $conn->close();
    
    return [
        'total_members' => $total_members,
        'recipes_shared' => $recipes_shared,
        'active_discussions' => $active_discussions,
        'events_this_month' => $events_this_month
    ];
}

function get_top_contributors($limit = 4) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("
        SELECT u.id, u.first_name, u.last_name, u.profile_image, COUNT(cp.id) as recipe_count
        FROM users u
        JOIN community_posts cp ON u.id = cp.user_id
        WHERE cp.post_type = 'recipe'
        GROUP BY u.id
        ORDER BY recipe_count DESC
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $contributors = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $contributors;
}
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    //$diff->w = floor($diff->d / 7);
    //$diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
       // 'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
// Call the request handler
handle_requests();
?>