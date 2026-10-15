<?php
require_once 'config.php';
require_once 'db_functions.php';
require_once 'E_resource.php';

echo "<h2>Database Test</h2>";

// Test connection
try {
    $conn = get_db_connection();
    echo "✓ Database connection successful<br>";
    
    // Test if table exists
    $result = $conn->query("SHOW TABLES LIKE 'educational_resources'");
    if ($result->num_rows > 0) {
        echo "✓ Table 'educational_resources' exists<br>";
        
        // Count records
        $count_result = $conn->query("SELECT COUNT(*) as count FROM educational_resources");
        $count = $count_result->fetch_assoc()['count'];
        echo "✓ Records in table: $count<br>";
        
        if ($count > 0) {
            // Show some sample data
            echo "<h3>Sample Data:</h3>";
            $data = $conn->query("SELECT id, title, type, is_active FROM educational_resources LIMIT 5");
            while ($row = $data->fetch_assoc()) {
                echo "ID: {$row['id']}, Title: {$row['title']}, Type: {$row['type']}, Active: {$row['is_active']}<br>";
            }
        }
    } else {
        echo "✗ Table 'educational_resources' does not exist<br>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
}

echo "<h2>Function Tests</h2>";

// Test your functions
try {
    $all_resources = get_all_resources();
    echo "get_all_resources() returned: " . count($all_resources) . " records<br>";
    
    $categories = get_resource_categories();
    echo "get_resource_categories() returned: " . count($categories) . " categories: " . implode(', ', $categories) . "<br>";
    
    $types = get_resource_types();
    echo "get_resource_types() returned: " . count($types) . " types: " . implode(', ', $types) . "<br>";
    
    $difficulty = get_difficulty_levels();
    echo "get_difficulty_levels() returned: " . count($difficulty) . " levels: " . implode(', ', $difficulty) . "<br>";
    
} catch (Exception $e) {
    echo "Function test failed: " . $e->getMessage() . "<br>";
}
?>