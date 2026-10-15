<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/db_functions.php';

$action = $_GET['action'] ?? 'list';
$recipe_id = $_GET['id'] ?? 0;
$my_recipes = isset($_GET['my_recipes']) && is_logged_in();

// Handle share to community action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'share_to_community' && is_logged_in()) {
    $recipe_id_to_share = (int)$_POST['recipe_id'];
    $recipe = get_recipe_by_id($recipe_id_to_share);
    
    if ($recipe) {
        // Create community post content
        $title = $recipe['title'];
        $content = $recipe['description'] . "\n\nIngredients:\n" . $recipe['ingredients'] . "\n\nInstructions:\n" . $recipe['instructions'];
        $image_path = $recipe['image_path'];
        
        // Save to community posts
        $post_id = create_community_post($_SESSION['user_id'], $title, $content, $image_path, 'recipe', $recipe_id_to_share);
        
        if ($post_id) {
            header("Location: " . SITE_URL . "/pages/community.php?recipe_shared=1");
            exit;
        } else {
            $error = "Failed to share recipe to community. Please try again.";
        }
    }
}

// Function to display recipe cards (reusable component)
function display_recipe_card($recipe, $is_my_recipe = false) {
    ob_start();
    ?>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card h-100 recipe-card">
            <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=view&id=<?php echo $recipe['id']; ?>">
                <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $recipe['image_path'] ?: 'default-recipe.jpg'; ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
            </a>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-success"><?php echo $recipe['cuisine_type'] ?: 'Global'; ?></span>
                    <small class="text-muted"><?php echo $recipe['difficulty']; ?></small>
                </div>
                <h5 class="card-title">
                    <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=view&id=<?php echo $recipe['id']; ?>" 
                       class="text-decoration-none text-dark">
                        <?php echo htmlspecialchars($recipe['title']); ?>
                    </a>
                </h5>
                <p class="card-text text-muted"><?php echo substr($recipe['description'], 0, 100); ?>...</p>
            </div>
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <?php echo date('M j, Y', strtotime($recipe['created_at'])); ?>
                    </small>
                    <?php if ($is_my_recipe): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" 
                                       href="<?php echo SITE_URL; ?>/pages/recipes.php?action=edit&id=<?php echo $recipe['id']; ?>">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger delete-recipe" href="#" data-id="<?php echo $recipe['id']; ?>">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Function to generate pagination (reusable component)
function generate_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) return '';
    
    ob_start();
    ?>
    <nav aria-label="Recipe pagination" class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $base_url . ($current_page - 1); ?>" tabindex="-1">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $base_url . ($current_page + 1); ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php
    return ob_get_clean();
}

if ($action === 'view' && $recipe_id) {
    // View single recipe
    $recipe = get_recipe_by_id($recipe_id);
    
    if (!$recipe) {
        header("Location: " . SITE_URL . "/pages/recipes.php");
        exit;
    }
    
    // Get recipe categories
    $categories = get_recipe_categories($recipe_id);
    // Calculate total pages for pagination
    $per_page = 12; // Number of items per page
    $total_recipes = count_recipes(); // You'll need to implement this function
    $total_pages = ceil($total_recipes / $per_page);
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $current_page = max(1, min($current_page, $total_pages)); // Ensure page is within bounds
    ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/pages/recipes.php">Recipes</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($recipe['title']); ?></li>
                    </ol>
                </nav>
                
                <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($recipe['title']); ?></h1>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        <?php if ($recipe['profile_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/profiles/<?php echo $recipe['profile_image']; ?>" 
                                 class="rounded-circle" width="50" height="50" alt="Profile">
                        <?php else: ?>
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($recipe['first_name'] . ' ' . htmlspecialchars($recipe['last_name'])); ?></p>
                        <small class="text-muted">
                            Posted on <?php echo date('F j, Y', strtotime($recipe['created_at'])); ?>
                        </small>
                    </div>
                </div>
                
                <div class="mb-4">
                    <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $recipe['image_path'] ?: 'default-recipe.jpg'; ?>" 
                         class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-clock text-success mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Prep Time</h5>
                                <p class="card-text"><?php echo $recipe['prep_time']; ?> mins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-fire text-success mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Cook Time</h5>
                                <p class="card-text"><?php echo $recipe['cook_time']; ?> mins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-utensils text-success mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Servings</h5>
                                <p class="card-text"><?php echo $recipe['servings']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-tachometer-alt text-success mb-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title">Difficulty</h5>
                                <p class="card-text"><?php echo $recipe['difficulty']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-5">
                    <h3 class="fw-bold mb-3">Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-5">
                        <h3 class="fw-bold mb-3">Ingredients</h3>
                        <ul class="list-group list-group-flush">
                            <?php 
                            $ingredients = explode("\n", $recipe['ingredients']);
                            foreach ($ingredients as $ingredient): 
                                if (trim($ingredient)): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars(trim($ingredient)); ?></li>
                                <?php endif; 
                            endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6 mb-5">
                        <h3 class="fw-bold mb-3">Instructions</h3>
                        <ol class="list-group list-group-numbered list-group-flush">
                            <?php 
                            $instructions = explode("\n", $recipe['instructions']);
                            foreach ($instructions as $instruction): 
                                if (trim($instruction)): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars(trim($instruction)); ?></li>
                                <?php endif; 
                            endforeach; ?>
                        </ol>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between border-top pt-4">
                    <div>
                        <?php if ($recipe['cuisine_type']): ?>
                            <span class="badge bg-success me-2"><?php echo $recipe['cuisine_type']; ?></span>
                        <?php endif; ?>
                        <?php if ($recipe['dietary_preferences']): ?>
                            <span class="badge bg-info"><?php echo $recipe['dietary_preferences']; ?></span>
                        <?php endif; ?>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($category['name']); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-success me-2"><i class="far fa-heart"></i> Save</button>
                        
                        <!-- Share Button with Modal Trigger -->
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#shareRecipeModal">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">More Recipes</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $similar_recipes = get_featured_recipes(3);
                        foreach ($similar_recipes as $similar): 
                            if ($similar['id'] != $recipe_id): ?>
                                <div class="mb-3">
                                    <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=view&id=<?php echo $similar['id']; ?>" 
                                       class="text-decoration-none">
                                        <div class="d-flex">
                                            <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $similar['image_path'] ?: 'default-recipe.jpg'; ?>" 
                                                 class="rounded me-3" width="80" height="80" alt="<?php echo htmlspecialchars($similar['title']); ?>">
                                            <div>
                                                <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($similar['title']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo $similar['first_name'] . ' ' . $similar['last_name']; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; 
                        endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Share Recipe Modal -->
    <div class="modal fade" id="shareRecipeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Share Recipe to Community</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (is_logged_in()): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="share_to_community">
                            <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                            
                            <p>Are you sure you want to share this recipe to the community?</p>
                            <p class="text-muted">Your recipe will be visible to all community members.</p>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Share to Community</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                            <h4>Please log in to share recipes</h4>
                            <p class="mb-4">You need to be logged in to share recipes to the community.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn btn-success px-4">Login</a>
                                <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-outline-success px-4">Register</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
} elseif ($action === 'add' || $action === 'edit') {
    // Add or edit recipe form
    if (!is_logged_in()) {
        header("Location: " . SITE_URL . "/pages/login.php");
        exit;
    }
    
    $recipe = null;
    if ($action === 'edit' && $recipe_id) {
        $recipe = get_recipe_by_id($recipe_id);
        if (!$recipe || $recipe['user_id'] != $_SESSION['user_id']) {
            header("Location: " . SITE_URL . "/pages/recipes.php");
            exit;
        }
    }
    
    $categories = get_all_categories();
    $recipe_categories = $recipe ? array_column(get_recipe_categories($recipe_id), 'id') : [];
    ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-4">
                    <?php echo $action === 'add' ? 'Add New Recipe' : 'Edit Recipe'; ?>
                </h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        switch ($_GET['error']) {
                            case 'upload':
                                echo 'There was an error uploading your image. Please try again.';
                                break;
                            case 'db':
                                echo 'There was a database error. Please try again.';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="../includes/db_functions.php" enctype="multipart/form-data" id="recipeForm">
                    <input type="hidden" name="action" value="<?php echo $action === 'add' ? 'add_recipe' : 'update_recipe'; ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">Recipe Title *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo $recipe ? htmlspecialchars($recipe['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php 
                            echo $recipe ? htmlspecialchars($recipe['description']) : ''; 
                        ?></textarea>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="prep_time" class="form-label fw-bold">Prep Time (minutes) *</label>
                            <input type="number" class="form-control" id="prep_time" name="prep_time" min="0"
                                   value="<?php echo $recipe ? $recipe['prep_time'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cook_time" class="form-label fw-bold">Cook Time (minutes) *</label>
                            <input type="number" class="form-control" id="cook_time" name="cook_time" min="0"
                                   value="<?php echo $recipe ? $recipe['cook_time'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="servings" class="form-label fw-bold">Servings *</label>
                            <input type="number" class="form-control" id="servings" name="servings" min="1"
                                   value="<?php echo $recipe ? $recipe['servings'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="difficulty" class="form-label fw-bold">Difficulty *</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="Easy" <?php echo $recipe && $recipe['difficulty'] === 'Easy' ? 'selected' : ''; ?>>Easy</option>
                                <option value="Medium" <?php echo $recipe && $recipe['difficulty'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="Hard" <?php echo $recipe && $recipe['difficulty'] === 'Hard' ? 'selected' : ''; ?>>Hard</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="cuisine_type" class="form-label fw-bold">Cuisine Type</label>
                            <input type="text" class="form-control" id="cuisine_type" name="cuisine_type" 
                                   value="<?php echo $recipe ? htmlspecialchars($recipe['cuisine_type']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="dietary_preferences" class="form-label fw-bold">Dietary Preferences</label>
                            <input type="text" class="form-control" id="dietary_preferences" name="dietary_preferences" 
                                   value="<?php echo $recipe ? htmlspecialchars($recipe['dietary_preferences']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="ingredients" class="form-label fw-bold">Ingredients (one per line) *</label>
                        <textarea class="form-control" id="ingredients" name="ingredients" rows="5" required><?php 
                            echo $recipe ? htmlspecialchars($recipe['ingredients']) : ''; 
                        ?></textarea>
                        <small class="text-muted">Enter each ingredient on a new line</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="instructions" class="form-label fw-bold">Instructions (one step per line) *</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="5" required><?php 
                            echo $recipe ? htmlspecialchars($recipe['instructions']) : ''; 
                        ?></textarea>
                        <small class="text-muted">Enter each instruction step on a new line</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Recipe Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if ($recipe && $recipe['image_path']): ?>
                            <div class="mt-2">
                                <small>Current image:</small>
                                <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $recipe['image_path']; ?>" 
                                     class="img-thumbnail mt-1" width="100" alt="Current recipe image">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">
                                        Remove current image
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Categories</label>
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="category_<?php echo $category['id']; ?>" 
                                               name="categories[]" 
                                               value="<?php echo $category['id']; ?>"
                                               <?php if (in_array($category['id'], $recipe_categories)): ?>checked<?php endif; ?>>
                                        <label class="form-check-label" for="category_<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo $recipe ? SITE_URL . '/pages/recipes.php?action=view&id=' . $recipe_id : SITE_URL . '/pages/recipes.php'; ?>" 
                           class="btn btn-outline-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-success"><?php echo $action === 'add' ? 'Add Recipe' : 'Update Recipe'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('recipeForm').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const ingredients = document.getElementById('ingredients').value.trim();
        const instructions = document.getElementById('instructions').value.trim();
        
        if (!title || !description || !ingredients || !instructions) {
            e.preventDefault();
            alert('Please fill in all required fields (marked with *)');
            return false;
        }
        
        const fileInput = document.getElementById('image');
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!validTypes.includes(file.type)) {
                e.preventDefault();
                alert('Only JPG, PNG, and GIF images are allowed');
                return false;
            }
            
            if (file.size > maxSize) {
                e.preventDefault();
                alert('Image size must be less than 2MB');
                return false;
            }
        }
        
        return true;
    });
    </script>
    
    <?php
} else {
    // List all recipes or user's recipes
    if ($my_recipes) {
        $recipes = get_user_recipes($_SESSION['user_id']);
        $page_title = "My Recipes";
    } else {
        $recipes = get_all_recipes();
        $page_title = "Recipe Collection";
    }
    
    // Handle search
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $recipes = search_recipes($_GET['search']);
    }
    
    // Pagination setup
    $per_page = 12;
    $total_recipes = count($recipes);
    $total_pages = ceil($total_recipes / $per_page);
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $current_page = max(1, min($current_page, $total_pages));
    
    // Get current page recipes
    $offset = ($current_page - 1) * $per_page;
    $current_recipes = array_slice($recipes, $offset, $per_page);
    
    // Build base URL for pagination
    $base_url_params = [];
    if ($my_recipes) $base_url_params['my_recipes'] = 1;
    if (isset($_GET['search'])) $base_url_params['search'] = $_GET['search'];
    $base_url = SITE_URL . "/pages/recipes.php?" . http_build_query($base_url_params) . "&page=";
    ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="fw-bold"><?php echo $page_title; ?></h1>
            
            <?php if (is_logged_in()): ?>
                <div>
                    <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=add" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add Recipe
                    </a>
                    <?php if (!$my_recipes): ?>
                        <a href="<?php echo SITE_URL; ?>/pages/recipes.php?my_recipes=1" class="btn btn-outline-success ms-2">
                            <i class="fas fa-book me-2"></i>My Recipes
                        </a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/pages/recipes.php" class="btn btn-outline-success ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to All Recipes
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <form class="d-flex" method="GET" action="<?php echo SITE_URL; ?>/pages/recipes.php">
                    <input type="hidden" name="my_recipes" value="<?php echo $my_recipes ? '1' : '0'; ?>">
                    <input class="form-control me-2" type="search" placeholder="Search recipes..." 
                           name="search" value="<?php echo $_GET['search'] ?? ''; ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-success dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Cuisine Type</h6></li>
                            <li><a class="dropdown-item" href="?filter=cuisine&value=Italian">Italian</a></li>
                            <li><a class="dropdown-item" href="?filter=cuisine&value=Mexican">Mexican</a></li>
                            <li><a class="dropdown-item" href="?filter=cuisine&value=Asian">Asian</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Dietary</h6></li>
                            <li><a class="dropdown-item" href="?filter=dietary&value=Vegetarian">Vegetarian</a></li>
                            <li><a class="dropdown-item" href="?filter=dietary&value=Vegan">Vegan</a></li>
                            <li><a class="dropdown-item" href="?filter=dietary&value=Gluten-Free">Gluten-Free</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-sort me-2"></i>Sort
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="?sort=newest">Newest First</a></li>
                            <li><a class="dropdown-item" href="?sort=oldest">Oldest First</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?sort=popular">Most Popular</a></li>
                            <li><a class="dropdown-item" href="?sort=rating">Highest Rated</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (isset($_GET['search']) && empty($current_recipes)): ?>
            <div class="alert alert-info">
                No recipes found for "<?php echo htmlspecialchars($_GET['search']); ?>".
                <a href="<?php echo SITE_URL; ?>/pages/recipes.php" class="alert-link">View all recipes</a>
            </div>
        <?php elseif (empty($current_recipes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-4x text-muted mb-4"></i>
                <h3>No recipes found</h3>
                <p class="text-muted"><?php echo $my_recipes ? 'You haven\'t added any recipes yet.' : 'There are no recipes available at the moment.'; ?></p>
                <?php if (is_logged_in() && $my_recipes): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=add" class="btn btn-success mt-3">
                        <i class="fas fa-plus me-2"></i>Add Your First Recipe
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($current_recipes as $recipe): 
                    echo display_recipe_card($recipe, $my_recipes);
                endforeach; ?>
            </div>
            
            <?php echo generate_pagination($current_page, $total_pages, $base_url); ?>
        <?php endif; ?>
    </div>
    
    <?php if ($my_recipes): ?>
    <script>
    document.querySelectorAll('.delete-recipe').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const recipeId = this.getAttribute('data-id');
            
            if (confirm('Are you sure you want to delete this recipe?')) {
                fetch('../includes/db_functions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_recipe&id=${recipeId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error deleting recipe: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the recipe');
                });
            }
        });
    });
    </script>
    <?php endif; ?>
<?php
}
require_once '../includes/footer.php';
?>