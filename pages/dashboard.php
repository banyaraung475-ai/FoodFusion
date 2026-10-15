<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/auth_functions.php';

if (!is_logged_in()) {
    header("Location: " . SITE_URL . "/pages/login.php");
    exit;
}

// Initialize session variables with safe defaults
$user_first_name = $_SESSION['user_first_name'];
$user_last_name = $_SESSION['user_last_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';
$user_profile_image = $_SESSION['user_profile_image'] ?? null;
$user_created_at = $_SESSION['user_created_at'] ?? date('Y-m-d H:i:s');

$user_recipes = get_user_recipes($_SESSION['user_id']);

// Safe fallback for user_created_at with default value
$member_since = $user_created_at;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <?php if ($user_profile_image): ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/profiles/<?php echo $user_profile_image; ?>" 
                             class="rounded-circle mb-3" width="150" height="150" alt="Profile">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                             style="width: 150px; height: 150px;">
                            <i class="fas fa-user text-white fa-4x"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user_first_name . ' ' . htmlspecialchars($user_last_name)); ?></h4>
                    <p class="text-muted mb-4">Member since <?php echo date('F Y', strtotime($member_since)); ?></p>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Account Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Recipes Shared</span>
                        <span class="fw-bold"><?php echo count($user_recipes); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Community Posts</span>
                        <span class="fw-bold">18</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Recipes Saved</span>
                        <span class="fw-bold">24</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Events Attended</span>
                        <span class="fw-bold">3</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">My Dashboard</h2>
                <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=add" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Add Recipe
                </a>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">My Recipes</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($user_recipes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-utensils fa-4x text-muted mb-4"></i>
                            <h4>No recipes yet</h4>
                            <p class="text-muted mb-4">You haven't shared any recipes with the community yet.</p>
                            <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=add" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Add Your First Recipe
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Recipe</th>
                                        <th>Date</th>
                                        <th>Views</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user_recipes as $recipe): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=view&id=<?php echo $recipe['id']; ?>" 
                                                   class="text-decoration-none text-dark fw-bold">
                                                    <?php echo htmlspecialchars($recipe['title']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($recipe['created_at'])); ?></td>
                                            <td>142</td>
                                            <td>
                                                <span class="badge bg-success">Published</span>
                                            </td>
                                            <td>
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
                                                            <a class="dropdown-item" href="#">
                                                                <i class="fas fa-chart-line me-2"></i>Stats
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo SITE_URL; ?>/pages/recipes.php?my_recipes=1" class="btn btn-outline-success">
                                View All Recipes <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-heart text-success fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">Your recipe was liked</h6>
                            <p class="mb-1">"Homemade Pasta Carbonara" received 12 new likes</p>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-comment text-success fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">New comment on your recipe</h6>
                            <p class="mb-1">Maria Gonzalez commented: "This was delicious! I added some mushrooms too."</p>
                            <small class="text-muted">1 day ago</small>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-plus text-success fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">New follower</h6>
                            <p class="mb-1">David Kim started following you</p>
                            <small class="text-muted">2 days ago</small>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bookmark text-success fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1">Recipe saved</h6>
                            <p class="mb-1">You saved "Vegetable Stir Fry" to your favorites</p>
                            <small class="text-muted">3 days ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="profileImage" class="form-label">Profile Image</label>
                        <input class="form-control" type="file" id="profileImage" accept="image/*">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" value="<?php echo htmlspecialchars($user_first_name); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" value="<?php echo htmlspecialchars($user_last_name); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" value="<?php echo htmlspecialchars($user_email); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="editBio" class="form-label">Bio</label>
                        <textarea class="form-control" id="editBio" rows="3">Culinary enthusiast passionate about fusion cuisine and baking.</textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>