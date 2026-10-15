<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Success Modal for Login -->
            <?php if (isset($_SESSION['login_success'])): ?>
            <div class="modal fade show" id="loginSuccessModal" tabindex="-1" aria-labelledby="loginSuccessModalLabel" aria-modal="true" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="loginSuccessModalLabel">
                                <i class="fas fa-check-circle me-2"></i>Login Successful!
                            </h5>
                            <button type="button" class="btn-close btn-close-white" onclick="closeLoginModal()"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                            <h4 class="fw-bold mb-3">Welcome Back!</h4>
                            <p class="mb-0"><?php echo htmlspecialchars($_SESSION['login_success']); ?></p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-success" onclick="redirectToDashboard()">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function closeLoginModal() {
                    document.getElementById('loginSuccessModal').style.display = 'none';
                }
                
                function redirectToDashboard() {
                    window.location.href = "<?php echo SITE_URL; ?>/pages/dashboard.php";
                }
                
                // Auto-redirect to dashboard after 3 seconds
                setTimeout(function() {
                    redirectToDashboard();
                }, 3000);
            </script>
            <?php 
                unset($_SESSION['login_success']);
            endif; ?>
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="fw-bold mb-0">Welcome Back</h2>
                    <p class="mb-0">Sign in to your FoodFusion account</p>
                </div>
                <div class="card-body p-5">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="../includes/auth_functions.php?action=login">
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_SESSION['login_email'] ?? ''); ?>" required>
                            <?php unset($_SESSION['login_email']); ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="#" class="text-success">Forgot password?</a>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-success btn-lg">Sign In</button>
                        </div>
                        <div class="text-center">
                            <p class="mb-0">Don't have an account? <a href="<?php echo SITE_URL; ?>/pages/register.php" class="text-success fw-bold">Sign up</a></p>
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