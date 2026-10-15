<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/db_functions.php';

$featured_recipes = get_featured_recipes(4);
$community_posts = get_community_posts(10, 0);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'share_recipe' && is_logged_in()) {
            // Handle recipe sharing
            $title = trim($_POST['recipeTitle']);
            $description = trim($_POST['recipeDescription']);
            $ingredients = trim($_POST['recipeIngredients']);
            $instructions = trim($_POST['recipeInstructions']);
            
            // Process image upload
            $image_path = null;
            if (isset($_FILES['recipeImage']) && $_FILES['recipeImage']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/images/community/';
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['recipeImage']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['recipeImage']['tmp_name'], $target_path)) {
                    $image_path = $filename;
                }
            }
            
            // Create the post content
            $content = $description . "\n\nIngredients:\n" . $ingredients . "\n\nInstructions:\n" . $instructions;
            
            // Save to community posts
            $post_id = create_community_post($_SESSION['user_id'], $title, $content, $image_path, 'recipe');
            
            if ($post_id) {
                header("Location: community.php?post_shared=1");
                exit;
            } else {
                $error = "Failed to share recipe. Please try again.";
            }
        }
        elseif ($_POST['action'] === 'ask_question' && is_logged_in()) {
            // Handle question asking
            $title = trim($_POST['questionTitle']);
            $content = trim($_POST['questionDetails']);
            
            // Save to community posts
            $post_id = create_community_post($_SESSION['user_id'], $title, $content, null, 'question');
            
            if ($post_id) {
                header("Location: community.php?question_asked=1");
                exit;
            } else {
                $error = "Failed to post question. Please try again.";
            }
        }
        elseif ($_POST['action'] === 'add_comment' && is_logged_in()) {
            // Handle comment addition
            $post_id = (int)$_POST['post_id'];
            $content = trim($_POST['comment_content']);
            
            if ($content) {
                $comment_id = add_post_comment($post_id, $_SESSION['user_id'], $content);
                if ($comment_id) {
                    header("Location: community.php?comment_added=1#post-" . $post_id);
                    exit;
                }
            }
        }
    }
}

// Handle AJAX requests for likes
if (isset($_GET['ajax']) && $_GET['ajax'] === 'like' && is_logged_in()) {
    $post_id = (int)$_GET['post_id'];
    $result = toggle_post_like($post_id, $_SESSION['user_id']);
    echo json_encode($result);
    exit;
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-4">FoodFusion Community</h1>
            <p class="lead mb-5">Join our vibrant community of food enthusiasts to share recipes, cooking tips, and culinary experiences.</p>
            
            <?php if (isset($_GET['post_shared'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Your recipe has been shared successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['question_asked'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Your question has been posted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['comment_added'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Your comment has been added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['recipe_shared'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Recipe has been shared to the community successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card mb-5 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <?php if (is_logged_in() && isset($_SESSION['user_profile_image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/profiles/<?php echo $_SESSION['user_profile_image']; ?>" 
                                 class="rounded-circle me-3" width="60" height="60" alt="Profile">
                        <?php else: ?>
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-user text-white fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <input type="text" class="form-control form-control-lg" 
                               placeholder="Share your recipe or cooking tip..." 
                               data-bs-toggle="modal" data-bs-target="#shareRecipeModal">
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-success flex-grow-1 me-2" data-bs-toggle="modal" data-bs-target="#shareRecipeModal">
                            <i class="fas fa-utensils me-2"></i>Share Recipe
                        </button>
                        <button class="btn btn-outline-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                            <i class="fas fa-question-circle me-2"></i>Ask Question
                        </button>
                    </div>
                </div>
            </div>
            
            <h3 class="fw-bold mb-4">Recent Community Posts</h3>
            
            <?php if (empty($community_posts)): ?>
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h4>No posts yet</h4>
                        <p class="text-muted">Be the first to share a recipe or ask a question!</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($community_posts as $post): ?>
                    <div class="card mb-4 border-0 shadow-sm" id="post-<?php echo $post['id']; ?>">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($post['profile_image']): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/images/profiles/<?php echo $post['profile_image']; ?>" 
                                         class="rounded-circle me-3" width="50" height="50" alt="<?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>">
                                <?php else: ?>
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></h6>
                                    <small class="text-muted">Posted <?php echo time_ago($post['created_at']); ?></small>
                                    <?php if ($post['post_type'] === 'recipe'): ?>
                                        <span class="badge bg-success ms-2">Recipe</span>
                                    <?php else: ?>
                                        <span class="badge bg-info ms-2">Question</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="mb-3"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            
                            <?php if ($post['image_path']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $post['image_path']; ?>" 
                                     class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php endif; ?>
                            
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-success me-2 like-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="far fa-heart me-1"></i> <span class="like-count"><?php echo $post['like_count']; ?></span>
                                </button>
                                <button class="btn btn-sm btn-outline-success me-2" data-bs-toggle="collapse" 
                                        data-bs-target="#comments-<?php echo $post['id']; ?>">
                                    <i class="far fa-comment me-1"></i> <span class="comment-count"><?php echo $post['comment_count']; ?></span>
                                </button>
                                <button class="btn btn-sm btn-outline-success share-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="fas fa-share-alt me-1"></i> Share
                                </button>
                            </div>
                            
                            <!-- Comments Section -->
                            <div class="collapse mt-3" id="comments-<?php echo $post['id']; ?>">
                                <div class="border-top pt-3">
                                    <h6 class="fw-bold mb-3">Comments</h6>
                                    
                                    <?php 
                                    $comments = get_post_comments($post['id']);
                                    if (!empty($comments)): 
                                        foreach ($comments as $comment): ?>
                                            <div class="d-flex mb-3">
                                                <?php if ($comment['profile_image']): ?>
                                                    <img src="<?php echo SITE_URL; ?>/assets/images/profiles/<?php echo $comment['profile_image']; ?>" 
                                                         class="rounded-circle me-3" width="40" height="40" alt="<?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?>">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></h6>
                                                        <small class="text-muted"><?php echo time_ago($comment['created_at']); ?></small>
                                                    </div>
                                                    <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; 
                                    else: ?>
                                        <p class="text-muted">No comments yet. Be the first to comment!</p>
                                    <?php endif; ?>
                                    
                                    <?php if (is_logged_in()): ?>
                                        <form method="POST" class="mt-3">
                                            <input type="hidden" name="action" value="add_comment">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="comment_content" placeholder="Add a comment..." required>
                                                <button class="btn btn-success" type="submit">Post</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-info mt-3">
                                            <a href="<?php echo SITE_URL; ?>/pages/login.php" class="alert-link">Log in</a> to leave a comment.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <nav aria-label="Community pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Community Stats</h5>
                </div>
                <div class="card-body">
                    <?php
                    $stats = get_community_stats();
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Total Members</span>
                        <span class="fw-bold"><?php echo $stats['total_members']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Recipes Shared</span>
                        <span class="fw-bold"><?php echo $stats['recipes_shared']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Active Discussions</span>
                        <span class="fw-bold"><?php echo $stats['active_discussions']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Events This Month</span>
                        <span class="fw-bold"><?php echo $stats['events_this_month']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Top Contributors</h5>
                </div>
                <div class="card-body">
                    <?php
                    $top_contributors = get_top_contributors();
                    foreach ($top_contributors as $contributor): ?>
                        <div class="d-flex align-items-center mb-3">
                            <?php if ($contributor['profile_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/profiles/<?php echo $contributor['profile_image']; ?>" 
                                     class="rounded-circle me-3" width="50" height="50" alt="<?php echo htmlspecialchars($contributor['first_name'] . ' ' . $contributor['last_name']); ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($contributor['first_name'] . ' ' . $contributor['last_name']); ?></h6>
                                <small class="text-muted"><?php echo $contributor['recipe_count']; ?> recipes shared</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Popular Recipes</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($featured_recipes as $recipe): ?>
                        <div class="mb-3">
                            <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=view&id=<?php echo $recipe['id']; ?>" 
                               class="text-decoration-none">
                                <div class="d-flex">
                                    <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $recipe['image_path'] ?: 'default-recipe.jpg'; ?>" 
                                         class="rounded me-3" width="80" height="80" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                    <div>
                                        <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($recipe['title']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo $recipe['first_name'] . ' ' . $recipe['last_name']; ?>
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Recipe Modal -->
<div class="modal fade" id="shareRecipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Share Your Recipe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (is_logged_in()): ?>
                    <form method="POST" enctype="multipart/form-data" id="shareRecipeForm">
                        <input type="hidden" name="action" value="share_recipe">
                        <div class="mb-3">
                            <label for="recipeTitle" class="form-label">Recipe Title *</label>
                            <input type="text" class="form-control" id="recipeTitle" name="recipeTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="recipeDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="recipeDescription" name="recipeDescription" rows="3" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="recipeCuisine" class="form-label">Cuisine Type</label>
                                <select class="form-select" id="recipeCuisine" name="recipeCuisine">
                                    <option selected>Select cuisine</option>
                                    <option>Italian</option>
                                    <option>Mexican</option>
                                    <option>Asian</option>
                                    <option>Mediterranean</option>
                                    <option>American</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="recipeDifficulty" class="form-label">Difficulty</label>
                                <select class="form-select" id="recipeDifficulty" name="recipeDifficulty">
                                    <option selected>Select difficulty</option>
                                    <option>Easy</option>
                                    <option>Medium</option>
                                    <option>Hard</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="recipeIngredients" class="form-label">Ingredients (one per line) *</label>
                            <textarea class="form-control" id="recipeIngredients" name="recipeIngredients" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="recipeInstructions" class="form-label">Instructions (one step per line) *</label>
                            <textarea class="form-control" id="recipeInstructions" name="recipeInstructions" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="recipeImage" class="form-label">Recipe Image</label>
                            <input class="form-control" type="file" id="recipeImage" name="recipeImage" accept="image/*">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Share Recipe</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-user-lock fa-4x text-muted mb-4"></i>
                        <h4>Please log in to share recipes</h4>
                        <p class="mb-4">Join our community to share your culinary creations with other food enthusiasts.</p>
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

<!-- Ask Question Modal -->
<div class="modal fade" id="askQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Ask the Community</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (is_logged_in()): ?>
                    <form method="POST" id="askQuestionForm">
                        <input type="hidden" name="action" value="ask_question">
                        <div class="mb-3">
                            <label for="questionTitle" class="form-label">Question Title *</label>
                            <input type="text" class="form-control" id="questionTitle" name="questionTitle" placeholder="What's your cooking question?" required>
                        </div>
                        <div class="mb-3">
                            <label for="questionDetails" class="form-label">Details *</label>
                            <textarea class="form-control" id="questionDetails" name="questionDetails" rows="5" placeholder="Provide any relevant details..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="questionCategory" class="form-label">Category</label>
                            <select class="form-select" id="questionCategory" name="questionCategory">
                                <option selected>Select category</option>
                                <option>Baking</option>
                                <option>Grilling</option>
                                <option>Meal Prep</option>
                                <option>Vegetarian/Vegan</option>
                                <option>Kitchen Tools</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Post Question</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-user-lock fa-4x text-muted mb-4"></i>
                        <h4>Please log in to ask questions</h4>
                        <p class="mb-4">Join our community to get answers from experienced cooks and food enthusiasts.</p>
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
require_once '../includes/footer.php';
?>
<script>
// Handle like button clicks
document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        const likeCount = this.querySelector('.like-count');
        
        fetch('community.php?ajax=like&post_id=' + postId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const currentCount = parseInt(likeCount.textContent);
                    if (data.action === 'like') {
                        likeCount.textContent = currentCount + 1;
                        this.classList.add('active');
                    } else {
                        likeCount.textContent = currentCount - 1;
                        this.classList.remove('active');
                    }
                }
            });
    });
});

// Handle share button clicks
document.querySelectorAll('.share-btn').forEach(button => {
    button.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        const url = window.location.origin + window.location.pathname + '#post-' + postId;
        
        // Copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Post link copied to clipboard!');
        });
    });
});

// Form validation
document.getElementById('shareRecipeForm')?.addEventListener('submit', function(e) {
    const title = document.getElementById('recipeTitle').value.trim();
    const description = document.getElementById('recipeDescription').value.trim();
    const ingredients = document.getElementById('recipeIngredients').value.trim();
    const instructions = document.getElementById('recipeInstructions').value.trim();
    
    if (!title || !description || !ingredients || !instructions) {
        e.preventDefault();
        alert('Please fill in all required fields (marked with *)');
        return false;
    }
});

document.getElementById('askQuestionForm')?.addEventListener('submit', function(e) {
    const title = document.getElementById('questionTitle').value.trim();
    const details = document.getElementById('questionDetails').value.trim();
    
    if (!title || !details) {
        e.preventDefault();
        alert('Please fill in all required fields (marked with *)');
        return false;
    }
});
</script>

