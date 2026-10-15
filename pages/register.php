<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/auth_functions.php';

// Check if there's a success message from registration
$registration_success = false;
$success_message = '';

// Check for success message from auth_functions
if (isset($_SESSION['registration_success'])) {
    $registration_success = true;
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

// Clear any old form data if this is a fresh load (not after form submission)
if (!isset($_POST['register']) && !isset($_SESSION['errors'])) {
    unset($_SESSION['form_data']);
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Success Modal -->
            <?php if ($registration_success): ?>
            <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-modal="true" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="successModalLabel">
                                <i class="fas fa-check-circle me-2"></i>Success!
                            </h5>
                            <button type="button" class="btn-close btn-close-white" onclick="closeModal()"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                            <h4 class="fw-bold mb-3">Registration Successful!</h4>
                            <p class="mb-0"><?php echo htmlspecialchars($success_message); ?></p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-success" onclick="redirectToLogin()">
                                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function closeModal() {
                    document.getElementById('successModal').style.display = 'none';
                }
                
                function redirectToLogin() {
                    window.location.href = "<?php echo SITE_URL; ?>/pages/login.php";
                }
                
                // Auto-redirect after 5 seconds
                setTimeout(function() {
                    redirectToLogin();
                }, 5000);
            </script>
            <?php endif; ?>
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="fw-bold mb-0">Join FoodFusion</h2>
                    <p class="mb-0">Create your free account</p>
                </div>
                <div class="card-body p-5">                    
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                                <?php unset($_SESSION['errors']); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="../includes/auth_functions.php?action=register">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                       value="<?php echo htmlspecialchars($_SESSION['form_data']['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                       value="<?php echo htmlspecialchars($_SESSION['form_data']['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" required>
                            <label class="form-check-label" for="agreeTerms">
                                I agree to the <a href="#" class="text-success">Terms of Service</a> and <a href="#" class="text-success">Privacy Policy</a>
                            </label>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-success btn-lg" name="register">Create Account</button>
                        </div>
                        <div class="text-center">
                            <p class="mb-0">Already have an account? <a href="<?php echo SITE_URL; ?>/pages/login.php" class="text-success fw-bold">Sign in</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>