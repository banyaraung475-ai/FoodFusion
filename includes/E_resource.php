<?php
require_once 'config.php';
require_once 'db_functions.php';

// Check if functions already exist before declaring them
if (!function_exists('get_all_resources')) {
    function get_all_resources($type = null, $category = null, $difficulty = null) {
        $sql = "SELECT * FROM educational_resources WHERE is_active = 1";
        $params = [];
        
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $sql .= " AND difficulty_level = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        error_log("SQL Query: " . $sql);
        error_log("Params: " . print_r($params, true));
        
        $result = db_query($sql, $params);
        error_log("Query result count: " . (is_array($result) ? count($result) : 'NOT_ARRAY'));
        
        return is_array($result) ? $result : [];
    }
}

if (!function_exists('get_resource_categories')) {
    function get_resource_categories() {
        $sql = "SELECT DISTINCT category FROM educational_resources WHERE category IS NOT NULL AND is_active = 1 ORDER BY category";
        error_log("Categories SQL: " . $sql);
        
        $results = db_query($sql);
        
        // Check if results is an array and not empty
        if (is_array($results) && count($results) > 0) {
            error_log("Categories found: " . print_r($results, true));
            return array_column($results, 'category');
        }
        
        error_log("No categories found or empty result");
        return [];
    }
}

if (!function_exists('get_resource_types')) {
    function get_resource_types() {
        $sql = "SELECT DISTINCT type FROM educational_resources WHERE is_active = 1 ORDER BY type";
        error_log("Types SQL: " . $sql);
        
        $results = db_query($sql);
        
        // Check if results is an array and not empty
        if (is_array($results) && count($results) > 0) {
            error_log("Types found: " . print_r($results, true));
            return array_column($results, 'type');
        }
        
        error_log("No types found or empty result");
        return [];
    }
}

if (!function_exists('get_difficulty_levels')) {
    function get_difficulty_levels() {
        $sql = "SELECT DISTINCT difficulty_level FROM educational_resources WHERE difficulty_level IS NOT NULL AND is_active = 1 ORDER BY difficulty_level";
        error_log("Difficulty SQL: " . $sql);
        
        $results = db_query($sql);
        
        // Check if results is an array and not empty
        if (is_array($results) && count($results) > 0) {
            error_log("Difficulty levels found: " . print_r($results, true));
            return array_column($results, 'difficulty_level');
        }
        
        error_log("No difficulty levels found or empty result");
        return [];
    }
}
// In db_functions.php, update the db_query function to handle different parameter types

// ... keep the other functions the same but add error logging ...
?>