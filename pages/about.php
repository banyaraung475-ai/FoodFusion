<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/db_functions.php';

/**
 * Fetches about page data from database
 * Returns an associative array with all data or empty arrays on error
 */
function getAboutPageData() {
    $data = [
        'teamMembers' => [],
        'testimonials' => [],
        'missions' => [],
        'philosophies' => [],
        'historyItems' => []
    ];
    
    try {
        // Get team members
        $data['teamMembers'] = db_query("SELECT * FROM team_members WHERE is_active = TRUE ORDER BY display_order");
        
        // Get testimonials
        $data['testimonials'] = db_query("SELECT * FROM testimonials WHERE is_active = TRUE ORDER BY display_order");
        
        // Get company missions
        $data['missions'] = db_query("SELECT * FROM company_info WHERE mission_title IS NOT NULL ORDER BY display_order");
        
        // Get company philosophies
        $data['philosophies'] = db_query("SELECT * FROM company_info WHERE philosophy_title IS NOT NULL ORDER BY display_order");
        
        // Get company history
        $data['historyItems'] = db_query("SELECT * FROM company_history ORDER BY display_order");
        
    } catch (Exception $e) {
        // Handle database errors
        error_log("Database error in getAboutPageData(): " . $e->getMessage());
        // Return empty arrays on error
    }
    
    return $data;
}

// Fetch data using the function
$pageData = getAboutPageData();

// Extract data for use in the template
$teamMembers = $pageData['teamMembers'];
$testimonials = $pageData['testimonials'];
$missions = $pageData['missions'];
$philosophies = $pageData['philosophies'];
$historyItems = $pageData['historyItems'];
?>
<!-- About Hero Section -->
<section class="about-hero bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Our Culinary Journey</h1>
                <p class="lead mb-4">Discover the story behind FoodFusion and our passion for bringing people together through food.</p>
                <a href="#our-mission" class="btn btn-success btn-lg px-4 me-2">Our Mission</a>
                <a href="#our-team" class="btn btn-outline-success btn-lg px-4">Meet the Team</a>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80" 
                     alt="Cooking team" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4">How FoodFusion Began</h2>
                <p class="lead text-muted mb-5">From a simple idea to a thriving culinary community</p>
                
                <div class="timeline">
                    <?php if (!empty($historyItems)): ?>
                        <?php foreach ($historyItems as $item): ?>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <h4 class="fw-bold"><?php echo htmlspecialchars($item['year']); ?></h4>
                                    <p><?php echo htmlspecialchars($item['event_description']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback content if no history items in database -->
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h4 class="fw-bold">2015</h4>
                                <p>Founded by chef Maria Rodriguez as a small blog sharing family recipes</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h4 class="fw-bold">2017</h4>
                                <p>Launched our first community cookbook with recipes from 50 contributors</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h4 class="fw-bold">2019</h4>
                                <p>Reached 10,000 members and hosted our first live cooking event</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h4 class="fw-bold">2022</h4>
                                <p>Redesigned platform launched with enhanced community features</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <h4 class="fw-bold">Today</h4>
                                <p>A global community of food lovers sharing, learning, and creating together</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission Section -->
<section id="our-mission" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1466637574441-749b8f19452f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80" 
                     alt="Our mission" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Our Mission & Values</h2>
                
                <?php if (!empty($missions)): ?>
                    <?php foreach ($missions as $mission): ?>
                        <div class="mission-item d-flex mb-4">
                            <div class="mission-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-4">
                                <i class="<?php echo htmlspecialchars($mission['mission_icon']); ?> fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold"><?php echo htmlspecialchars($mission['mission_title']); ?></h4>
                                <p class="mb-0"><?php echo htmlspecialchars($mission['mission_description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback content if no missions in database -->
                    <div class="mission-item d-flex mb-4">
                        <div class="mission-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-4">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Celebrate Culinary Diversity</h4>
                            <p class="mb-0">We believe every cuisine and cooking tradition has value and deserves to be shared.</p>
                        </div>
                    </div>
                    <div class="mission-item d-flex mb-4">
                        <div class="mission-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-4">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Build Community</h4>
                            <p class="mb-0">Food brings people together. We create spaces for meaningful culinary connections.</p>
                        </div>
                    </div>
                    <div class="mission-item d-flex mb-4">
                        <div class="mission-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-4">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Educate & Inspire</h4>
                            <p class="mb-0">We make cooking techniques accessible to all skill levels through quality resources.</p>
                        </div>
                    </div>
                    <div class="mission-item d-flex">
                        <div class="mission-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-4">
                            <i class="fas fa-leaf fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Promote Sustainability</h4>
                            <p class="mb-0">We advocate for responsible sourcing and minimizing food waste in home kitchens.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Culinary Philosophy Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h2 class="fw-bold mb-4">Our Culinary Philosophy</h2>
                <p class="lead mb-5">At FoodFusion, we believe cooking should be joyful, creative, and accessible to everyone.</p>
                
                <div class="row g-4">
                    <?php if (!empty($philosophies)): ?>
                        <?php foreach ($philosophies as $philosophy): ?>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="philosophy-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-4">
                                            <i class="<?php echo htmlspecialchars($philosophy['philosophy_icon']); ?> fa-2x"></i>
                                        </div>
                                        <h4 class="fw-bold"><?php echo htmlspecialchars($philosophy['philosophy_title']); ?></h4>
                                        <p><?php echo htmlspecialchars($philosophy['philosophy_description']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback content if no philosophies in database -->
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="philosophy-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-4">
                                        <i class="fas fa-heart fa-2x"></i>
                                    </div>
                                    <h4 class="fw-bold">Passion First</h4>
                                    <p>Cooking should come from the heart. We encourage recipes that tell stories and create memories.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="philosophy-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-4">
                                        <i class="fas fa-lightbulb fa-2x"></i>
                                    </div>
                                    <h4 class="fw-bold">Creativity Welcome</h4>
                                    <p>There are no mistakes in the kitchen, only new flavor combinations waiting to be discovered.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="philosophy-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-4">
                                        <i class="fas fa-hands-helping fa-2x"></i>
                                    </div>
                                    <h4 class="fw-bold">Community Focused</h4>
                                    <p>We grow stronger by sharing knowledge and supporting each other's culinary journeys.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section id="our-team" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <h2 class="fw-bold mb-3">Meet Our Team</h2>
                <p class="lead text-muted">The passionate food enthusiasts behind FoodFusion</p>
            </div>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php if (!empty($teamMembers)): ?>
                <?php foreach ($teamMembers as $member): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card team-card border-0 shadow-sm h-100">
                            <img src="<?php echo htmlspecialchars($member['image_url']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($member['name']); ?>">
                            <div class="card-body text-center">
                                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($member['name']); ?></h4>
                                <p class="text-success mb-3"><?php echo htmlspecialchars($member['position']); ?></p>
                                <p class="mb-3"><?php echo htmlspecialchars($member['bio']); ?></p>
                                <div class="social-links">
                                    <?php if (!empty($member['twitter_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['twitter_url']); ?>" class="text-success me-2"><i class="fab fa-twitter"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['instagram_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['instagram_url']); ?>" class="text-success me-2"><i class="fab fa-instagram"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['linkedin_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" class="text-success me-2"><i class="fab fa-linkedin"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['github_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['github_url']); ?>" class="text-success"><i class="fab fa-github"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no team members in database -->
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card team-card border-0 shadow-sm h-100">
                        <img src="https://images.pexels.com/photos/3763188/pexels-photo-3763188.jpeg?auto=compress&cs=tinysrgb&w=600&h=800&dpr=1" 
                             class="card-img-top" alt="Maria Rodriguez">
                        <div class="card-body text-center">
                            <h4 class="fw-bold mb-1">Maria Rodriguez</h4>
                            <p class="text-success mb-3">Founder & Head Chef</p>
                            <p class="mb-3">Professional chef with 15 years experience in international cuisines and culinary education.</p>
                            <div class="social-links">
                                <a href="#" class="text-success me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-success me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="text-success"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card team-card border-0 shadow-sm h-100">
                        <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=600&h=800&dpr=1" 
                             class="card-img-top" alt="David Kim">
                        <div class="card-body text-center">
                            <h4 class="fw-bold mb-1">David Kim</h4>
                            <p class="text-success mb-3">Community Manager</p>
                            <p class="mb-3">Food blogger and community builder with a passion for connecting people through cooking.</p>
                            <div class="social-links">
                                <a href="#" class="text-success me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-success me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="text-success"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card team-card border-0 shadow-sm h-100">
                        <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=600&h=800&dpr=1" 
                             class="card-img-top" alt="Sarah Johnson">
                        <div class="card-body text-center">
                            <h4 class="fw-bold mb-1">Sarah Johnson</h4>
                            <p class="text-success mb-3">Content Director</p>
                            <p class="mb-3">Cookbook author and food photographer dedicated to creating beautiful, accessible recipes.</p>
                            <div class="social-links">
                                <a href="#" class="text-success me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-success me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="text-success"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card team-card border-0 shadow-sm h-100">
                        <img src="https://images.pexels.com/photos/2182970/pexels-photo-2182970.jpeg?auto=compress&cs=tinysrgb&w=600&h=800&dpr=1" 
                             class="card-img-top" alt="James Wilson">
                        <div class="card-body text-center">
                            <h4 class="fw-bold mb-1">James Wilson</h4>
                            <p class="text-success mb-3">Tech Lead</p>
                            <p class="mb-3">Developer and food tech enthusiast building the platform that powers our community.</p>
                            <div class="social-links">
                                <a href="#" class="text-success me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-success me-2"><i class="fab fa-github"></i></a>
                                <a href="#" class="text-success"><i class="fab fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <h2 class="fw-bold mb-3">What Our Community Says</h2>
                <p class="lead text-muted">Hear from fellow food enthusiasts</p>
            </div>
        </div>
        
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php if (!empty($testimonials)): ?>
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="testimonial-card card border-0 shadow-sm mx-auto" style="max-width: 700px;">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="d-flex align-items-center mb-4">
                                        <img src="<?php echo htmlspecialchars($testimonial['image_url']); ?>" 
                                             class="rounded-circle me-3" width="60" height="60" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                        <div>
                                            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                                            <p class="text-muted mb-0"><?php echo htmlspecialchars($testimonial['role']); ?></p>
                                        </div>
                                    </div>
                                    <p class="lead mb-0">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback content if no testimonials in database -->
                    <div class="carousel-item active">
                        <div class="testimonial-card card border-0 shadow-sm mx-auto" style="max-width: 700px;">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&dpr=1" 
                                         class="rounded-circle me-3" width="60" height="60" alt="Emma Chen">
                                    <div>
                                        <h5 class="fw-bold mb-0">Emma Chen</h5>
                                        <p class="text-muted mb-0">Home Cook & Community Member</p>
                                    </div>
                                </div>
                                <p class="lead mb-0">"FoodFusion transformed my cooking. From basic recipes to advanced techniques, the community has supported me every step of the way. I've made friends from around the world who share my passion for food."</p>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="testimonial-card card border-0 shadow-sm mx-auto" style="max-width: 700px;">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="https://images.pexels.com/photos/1043474/pexels-photo-1043474.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&dpr=1" 
                                         class="rounded-circle me-3" width="60" height="60" alt="Michael Brown">
                                    <div>
                                        <h5 class="fw-bold mb-0">Michael Brown</h5>
                                        <p class="text-muted mb-0">Professional Chef</p>
                                    </div>
                                </div>
                                <p class="lead mb-0">"As a professional chef, I appreciate how FoodFusion bridges the gap between home cooks and professionals. The platform's respect for culinary traditions while encouraging innovation is truly special."</p>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="testimonial-card card border-0 shadow-sm mx-auto" style="max-width: 700px;">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="https://images.pexels.com/photos/3764160/pexels-photo-3764160.jpeg?auto=compress&cs=tinysrgb&w=600&h=600&dpr=1" 
                                         class="rounded-circle me-3" width="60" height="60" alt="Priya Patel">
                                    <div>
                                        <h5 class="fw-bold mb-0">Priya Patel</h5>
                                        <p class="text-muted mb-0">Food Blogger</p>
                                    </div>
                                </div>
                                <p class="lead mb-0">"The resources and community at FoodFusion helped me turn my passion into a career. Their commitment to authentic representation of global cuisines sets them apart in the food space."</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

<!-- Join Community CTA -->
<section class="py-5 bg-success text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4">Ready to Join Our Culinary Community?</h2>
                <p class="lead mb-5">Become part of a global network of food enthusiasts sharing, learning, and creating together.</p>
                <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-light btn-lg px-4 me-2">Sign Up Free</a>
                <a href="<?php echo SITE_URL; ?>/pages/community.php" class="btn btn-outline-light btn-lg px-4">Explore Community</a>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>