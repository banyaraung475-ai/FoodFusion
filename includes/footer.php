    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>About FoodFusion</h5>
                    <p>Bringing culinary creativity to your kitchen with recipes from around the world and a vibrant community of food enthusiasts.</p>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/YourFoodFusionPage" class="text-white me-2" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/YourFoodFusion" class="text-white me-2" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/YourFoodFusion" class="text-white me-2" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.pinterest.com/YourFoodFusion" class="text-white me-2" target="_blank" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
                        <a href="https://www.youtube.com/YourFoodFusion" class="text-white me-2" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-white">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/about.php" class="text-white">About</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/recipes.php" class="text-white">Recipes</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/community.php" class="text-white">Community</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/resources.php" class="text-white">Culinary Resources</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/EResources.php" class="text-white">Educational Resources</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Newsletter</h5>
                    <p>Subscribe to get updates on new recipes and events.</p>
                    <form class="mb-3" id="newsletterForm">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email" required>
                            <button class="btn btn-success" type="submit">Subscribe</button>
                        </div>
                    </form>
                    <!-- Social Sharing Buttons -->
                    <div class="mt-3">
                        <p class="small mb-1">Share FoodFusion:</p>
                        <div class="social-share-buttons">
                            <button class="btn btn-sm btn-outline-light me-1 share-btn" data-platform="facebook" title="Share on Facebook">
                                <i class="fab fa-facebook-f me-1"></i>Share
                            </button>
                            <button class="btn btn-sm btn-outline-light me-1 share-btn" data-platform="twitter" title="Tweet about us">
                                <i class="fab fa-twitter me-1"></i>Tweet
                            </button>
                            <button class="btn btn-sm btn-outline-light share-btn" data-platform="pinterest" title="Pin on Pinterest">
                                <i class="fab fa-pinterest me-1"></i>Pin
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Culinary Street, Foodville</li>
                        <li><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i> info@foodfusion.com</li>
                    </ul>
                  
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> FoodFusion. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white me-3">Privacy Policy</a>
                    <a href="#" class="text-white me-3">Terms of Service</a>
                    <a href="#" class="text-white">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent Banner -->
    <div id="cookieConsent" class="alert alert-dark text-center mb-0 rounded-0" style="display: none;">
        <div class="container">
            <span>We use cookies to enhance your experience. By continuing to visit this site, you agree to our use of cookies.</span>
            <button type="button" class="btn btn-sm btn-success ms-3" id="acceptCookies">Accept</button>
            <a href="#" class="btn btn-sm btn-outline-light ms-2">Learn More</a>
        </div>
    </div>

    <!-- Join Us Modal -->
    <div class="modal fade" id="joinUsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Join FoodFusion Community</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="joinForm" action="<?php echo SITE_URL; ?>/includes/auth_functions.php?action=register" method="POST">
                        <input type="hidden" name="redirect_to" value="login">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        
                        <!-- Social Login Options -->
                        <div class="mb-3">
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-primary" onclick="socialLogin('facebook')">
                                    <i class="fab fa-facebook-f me-2"></i>Sign up with Facebook
                                </a>
                                <a href="#" class="btn btn-light" onclick="socialLogin('google')">
                                    <i class="fab fa-google me-2"></i>Sign up with Google
                                </a>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" required>
                            <label class="form-check-label" for="agreeTerms">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Join Now</button>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="mb-0">Already a member? <a href="<?php echo SITE_URL; ?>/pages/login.php">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    
    <!-- Social Media Integration Script -->
    <script>
        // Social sharing functionality
        document.querySelectorAll('.share-btn').forEach(button => {
            button.addEventListener('click', function() {
                const platform = this.getAttribute('data-platform');
                const url = encodeURIComponent(window.location.href);
                const title = encodeURIComponent(document.title);
                let shareUrl;
                
                switch(platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                        break;
                    case 'pinterest':
                        // For Pinterest, we need an image - using site logo as fallback
                        const image = encodeURIComponent('<?php echo SITE_URL; ?>/assets/images/logo.png');
                        shareUrl = `https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`;
                        break;
                }
                
                window.open(shareUrl, '_blank', 'width=600,height=400');
            });
        });
        
        // Social login function
        function socialLogin(provider) {
            // Redirect to social login endpoint
            window.location.href = `<?php echo SITE_URL; ?>/includes/social_login.php?provider=${provider}`;
        }
        
        // Newsletter form submission
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Here you would typically send this to your server
            console.log('Newsletter subscription:', email);
            alert('Thank you for subscribing to our newsletter!');
            this.reset();
        });
        
        // Initialize social tools when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Any additional social media integration initialization
        });
    </script>
</body>
</html>