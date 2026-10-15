<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Initialize variables
$name = $email = $event = $comments = "";
$name_err = $email_err = $event_err = "";
$success_msg = "";

// Initialize mysqli to null to prevent undefined variable errors
$mysqli = null;

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Validate event selection
    if (empty($_POST["event"])) {
        $event_err = "Please select an event.";
    } else {
        $event = $_POST["event"];
    }
    
    // Get comments (optional)
    $comments = trim($_POST["comments"]);
    
    // Check input errors before inserting into database
    if (empty($name_err) && empty($email_err) && empty($event_err)) {
        // Create database connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        
        // Prepare an insert statement
        $sql = "INSERT INTO event_registrations (name, email, event_id, comments) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssis", $param_name, $param_email, $param_event, $param_comments);
            
            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_event = $event;
            $param_comments = $comments;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $success_msg = "Registration successful! We've sent a confirmation email to $email.";
                
                // Clear form fields
                $name = $email = $event = $comments = "";
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            $stmt->close();
        }
        
        // Close connection
        $mysqli->close();
    }
}

// Fetch events from database
$events = [];
// Only try to fetch events if we have a valid connection
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    // Fixed SQL query - removed erroneous WHERE clause
    $sql = "SELECT id, title, event_date FROM events ORDER BY event_date ASC";
    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $result->free();
    }
    
    // Close connection
    $mysqli->close();
} catch (Exception $e) {
    // Handle database error
    $db_error = "Unable to load events. Please try again later.";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Event Registration</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_msg; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($db_error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $db_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" 
                                   id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
                            <div class="invalid-feedback">
                                <?php echo $name_err; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <div class="invalid-feedback">
                                <?php echo $email_err; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="event" class="form-label">Select Event</label>
                            <select class="form-select <?php echo (!empty($event_err)) ? 'is-invalid' : ''; ?>" 
                                    id="event" name="event">
                                <option value="">Choose an event...</option>
                                <?php foreach ($events as $ev): ?>
                                    <option value="<?php echo $ev['id']; ?>" <?php echo ($event == $ev['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ev['title'] . ' (' . date('M j, Y', strtotime($ev['event_date'])) . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php echo $event_err; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comments" class="form-label">Additional Comments (Optional)</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3"><?php echo htmlspecialchars($comments); ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Register Now</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4">
                <h4>Upcoming Events</h4>
                <div class="list-group">
                    <?php if (count($events) > 0): ?>
                        <?php foreach ($events as $ev): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($ev['title']); ?></h5>
                                    <small><?php echo date('M j, Y', strtotime($ev['event_date'])); ?></small>
                                </div>
                                <p class="mb-1">Join us for this exciting event!</p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item">
                            <p class="mb-1">No upcoming events at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>