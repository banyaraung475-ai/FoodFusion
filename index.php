<?php
require_once 'includes/config.php';
require_once 'includes/header.php';


$featured_recipes = get_featured_recipes();
$upcoming_events = get_upcoming_events(6); // Get up to 6 upcoming events

// Split events into groups of 3 for carousel slides
$eventChunks = array_chunk($upcoming_events, 3);

 $is_logged_in = is_logged_in();
?>

<!-- Hero Section with Gradient Background -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4f0f5 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 pe-lg-5">
                <h1 class="display-4 fw-bold mb-4">Discover the <span class="text-success">Joy</span> of Cooking</h1>
                <p class="lead mb-4 text-muted">Join FoodFusion's vibrant community of culinary enthusiasts sharing, learning, and creating delicious recipes from around the world.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#featured-recipes" class="btn btn-success btn-lg px-4 py-3 shadow-sm">
                        <i class="fas fa-utensils me-2"></i> Explore Recipes
                    </a>
                    <button class="btn btn-outline-success btn-lg px-4 py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#joinUsModal">
                        <i class="fas fa-users me-2"></i> Join Community
                    </button>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="hero-image-container position-relative rounded-4 overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-4.0.3&w=1200&auto=format&fit=crop" 
                         alt="Fresh cooking ingredients" 
                         class="img-fluid w-100 h-auto" 
                         style="min-height: 400px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 end-0 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.3));">
                        <p class="text-white mb-0 small">Photo by <a href="https://unsplash.com" class="text-white" target="_blank">Unsplash</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Recipes Section -->
<!-- Featured Recipes -->
<section id="featured-recipes" class="py-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold">Featured Recipes</h2>
            <a href="<?php echo SITE_URL; ?>/pages/recipes.php" class="btn btn-outline-success">View All</a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_recipes as $recipe): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 recipe-card">
                        <img src="<?php echo SITE_URL; ?>/assets/images/recipes/<?php echo $recipe['image_path'] ?: 'default-recipe.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success"><?php echo $recipe['cuisine_type'] ?: 'Global'; ?></span>
                                <small class="text-muted"><?php echo $recipe['difficulty']; ?></small>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text text-muted"><?php echo substr($recipe['description'], 0, 100); ?>...</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo SITE_URL; ?>/pages/recipes.php?action=view&id=<?php echo $recipe['id']; ?>" 
                                   class="btn btn-sm btn-success">View Recipe</a>
                                <small class="text-muted">
                                    By <?php echo htmlspecialchars($recipe['first_name'] . ' ' . htmlspecialchars($recipe['last_name'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-5">Upcoming <span class="text-success">Cooking Events</span></h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Join our interactive cooking classes, workshops, and community gatherings</p>
        </div>
        
        <?php if (count($upcoming_events) > 0): ?>
        <div id="eventsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($eventChunks as $index => $chunk): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="row g-4 justify-content-center">
                        <?php foreach ($chunk as $event): ?>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow-lg">
                                <div class="card-img-top position-relative" style="height: 220px;">
                                    <img src="<?php echo htmlspecialchars($event['image_path'] ?? 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&h=400&q=80'); ?>" 
                                         class="w-100 h-100 object-cover"
                                         alt="<?php echo htmlspecialchars($event['title']); ?>">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-success bg-opacity-90 text-white">
                                            <i class="fas fa-calendar me-1"></i> <?php echo formatEventDate($event['event_date']); ?>
                                        </span>
                                    </div>
                                    <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(transparent, rgba(0,0,0,0.5));">
                                        <h5 class="card-title text-white mb-0"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        <small class="text-muted"><?php echo formatEventTime($event['event_date']); ?></small>
                                        <i class="fas fa-map-marker-alt text-muted ms-3 me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($event['location'] ?? 'TBA'); ?></small>
                                    </div>
                                    <p class="card-text mb-4"><?php 
                                        $description = $event['description'] ?? 'Join us for this exciting cooking event!';
                                        echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                    ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-users me-1"></i> Limited spots
                                        </span>
                                        <a href="./pages/eventre.php" class="btn btn-sm btn-success px-3">
                                            Register <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Carousel Controls -->
            <?php if (count($eventChunks) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#eventsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bg-success rounded-circle p-3" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#eventsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon bg-success rounded-circle p-3" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-success px-4 py-2">
                View All Events <i class="fas fa-calendar-alt ms-2"></i>
            </a>
        </div>
        
        <?php else: ?>
        <div class="text-center py-4">
            <p class="text-muted">No upcoming events at the moment. Please check back later!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-5">What Our <span class="text-success">Community</span> Says</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Hear from fellow food enthusiasts about their experiences</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <div class="d-flex mb-3">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" class="rounded-circle me-3" width="60" height="60" alt="User">
                        <div>
                            <h6 class="mb-0 fw-bold">Sarah Johnson</h6>
                            <small class="text-muted">Home Cook</small>
                        </div>
                    </div>
                    <p class="text-muted">"FoodFusion transformed my cooking skills! The community recipes are incredible and the events are so much fun."</p>
                    <div class="text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <div class="d-flex mb-3">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" class="rounded-circle me-3" width="60" height="60" alt="User">
                        <div>
                            <h6 class="mb-0 fw-bold">Michael Chen</h6>
                            <small class="text-muted">Professional Chef</small>
                        </div>
                    </div>
                    <p class="text-muted">"As a professional, I'm impressed by the quality of content and the passion of this community. Highly recommend!"</p>
                    <div class="text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <div class="d-flex mb-3">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" class="rounded-circle me-3" width="60" height="60" alt="User">
                        <div>
                            <h6 class="mb-0 fw-bold">Emma Rodriguez</h6>
                            <small class="text-muted">Food Blogger</small>
                        </div>
                    </div>
                    <p class="text-muted">"Found so many unique recipes here that became instant hits on my blog. The community is supportive and creative."</p>
                    <div class="text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>