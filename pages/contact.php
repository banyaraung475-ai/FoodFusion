<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/db_functions.php';

function save_contact_message($name, $email, $subject, $message) {
    $conn = get_db_connection(); // Get MySQLi connection

    try {
        $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        // Prepare variables for binding (cannot use function returns directly)
        $clean_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $clean_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $clean_subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        $clean_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        
        // Bind parameters - must use variables, not function calls
        $stmt->bind_param("ssss", $clean_name, $clean_email, $clean_subject, $clean_message);
        
        $stmt->execute();
        
        $insert_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        
        return $insert_id;
    } catch (Exception $e) {
        error_log("Database error in save_contact_message: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Basic validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Name must be less than 100 characters.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (strlen($email) > 150) {
        $errors[] = "Email must be less than 150 characters.";
    }
    
    if (!empty($subject) && strlen($subject) > 200) {
        $errors[] = "Subject must be less than 200 characters.";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required.";
    } elseif (strlen($message) > 2000) {
        $errors[] = "Message must be less than 2000 characters.";
    }
    
    if (empty($errors)) {
        // Save to database
        $saved = save_contact_message($name, $email, $subject, $message);
        
        if ($saved) {
            // Send email notification
            $email_subject = "New Contact Message: " . ($subject ?: "No Subject");
            $email_message = "Name: $name\nEmail: $email\n\nMessage:\n$message";
            
            $_SESSION['success'] = "Thank you for your message! We'll get back to you within 24 hours.";
            header("Location: " . SITE_URL . "/pages/contact.php");
            exit;
        } else {
            $errors[] = "Sorry, there was an error sending your message. Please try again.";
            $_SESSION['errors'] = $errors;
        }
    } else {
        $_SESSION['errors'] = $errors;
    }
    
    // Store form data for repopulation
    $_SESSION['form_data'] = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    ];
    
    // Redirect to prevent form resubmission
    header("Location: " . SITE_URL . "/pages/contact.php");
    exit;
}

// Check for success/error messages from redirect
$success = $_SESSION['success'] ?? '';
$errors = $_SESSION['errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];

// Clear session messages after displaying
unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['form_data']);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-6">
            <h1 class="fw-bold mb-4">Contact Us</h1>
            <p class="lead mb-5">Have questions, suggestions, or feedback? We'd love to hear from you!</p>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo SITE_URL; ?>/pages/contact.php">
                <div class="mb-4">
                    <label for="name" class="form-label fw-bold">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="subject" class="form-label fw-bold">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" 
                           value="<?php echo htmlspecialchars($form_data['subject'] ?? ''); ?>">
                </div>
                
                <div class="mb-4">
                    <label for="message" class="form-label fw-bold">Your Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required><?php 
                        echo htmlspecialchars($form_data['message'] ?? ''); 
                    ?></textarea>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">Send Message</button>
                </div>
            </form>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4">Get in Touch</h3>
                    <p class="mb-5">Our team is here to help with any questions about recipes, the community, or our platform.</p>
                    
                    <div class="d-flex mb-4">
                        <div class="me-4 text-success">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Our Location</h5>
                            <p class="text-muted">123 Culinary Street<br>Foodville, CA 90210</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-4 text-success">
                            <i class="fas fa-phone fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Call Us</h5>
                            <p class="text-muted">(123) 456-7890<br>Mon-Fri, 9am-5pm PST</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-4 text-success">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Email Us</h5>
                            <p class="text-muted">info@foodfusion.com<br>We typically reply within 24 hours</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="me-4 text-success">
                            <i class="fas fa-share-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Follow Us</h5>
                            <div class="social-icons mt-2">
                                <a href="#" class="text-success me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                                <a href="#" class="text-success me-3"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-success me-3"><i class="fab fa-instagram fa-lg"></i></a>
                                <a href="#" class="text-success me-3"><i class="fab fa-pinterest fa-lg"></i></a>
                                <a href="#" class="text-success"><i class="fab fa-youtube fa-lg"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>