<?php
$page_title = "Get Quote";
include 'includes/header.php';
require_once 'config/database.php';

$quote_submitted = false;
$error_message = '';
$estimated_cost = 0;

// Simple pricing calculation function
function calculateShipping($weight, $service_type, $distance = 'local') {
    $base_rates = [
        'standard' => 8.99,
        'express' => 24.99,
        'same_day' => 39.99,
        'overnight' => 29.99
    ];
    
    $weight_multiplier = 1;
    if ($weight > 1) $weight_multiplier = 1.5;
    if ($weight > 5) $weight_multiplier = 2;
    if ($weight > 10) $weight_multiplier = 2.5;
    if ($weight > 20) $weight_multiplier = 3;
    
    $distance_multiplier = $distance === 'international' ? 3 : 1;
    
    return round($base_rates[$service_type] * $weight_multiplier * $distance_multiplier, 2);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pickup_address = trim($_POST['pickup_address'] ?? '');
    $pickup_city = trim($_POST['pickup_city'] ?? '');
    $pickup_state = trim($_POST['pickup_state'] ?? '');
    $pickup_zip = trim($_POST['pickup_zip'] ?? '');
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $delivery_city = trim($_POST['delivery_city'] ?? '');
    $delivery_state = trim($_POST['delivery_state'] ?? '');
    $delivery_zip = trim($_POST['delivery_zip'] ?? '');
    $package_weight = floatval($_POST['package_weight'] ?? 0);
    $package_length = trim($_POST['package_length'] ?? '');
    $package_width = trim($_POST['package_width'] ?? '');
    $package_height = trim($_POST['package_height'] ?? '');
    $service_type = trim($_POST['service_type'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    if (empty($name) || empty($email) || empty($pickup_address) || empty($delivery_address) || $package_weight <= 0 || empty($service_type)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            // Calculate estimated cost
            $distance_type = (strtolower($delivery_state) !== strtolower($pickup_state)) ? 'interstate' : 'local';
            $estimated_cost = calculateShipping($package_weight, $service_type, $distance_type);
            
            // Save quote to database
            $database = new Database();
            $db = $database->getConnection();
            
            $package_dimensions = '';
            if (!empty($package_length) && !empty($package_width) && !empty($package_height)) {
                $package_dimensions = $package_length . 'x' . $package_width . 'x' . $package_height;
            }
            
            $full_pickup_address = $pickup_address . ', ' . $pickup_city . ', ' . $pickup_state . ' ' . $pickup_zip;
            $full_delivery_address = $delivery_address . ', ' . $delivery_city . ', ' . $delivery_state . ' ' . $delivery_zip;
            
            $query = "INSERT INTO quotes (name, email, phone, pickup_address, delivery_address, package_weight, package_dimensions, service_type, estimated_cost, notes) 
                      VALUES (:name, :email, :phone, :pickup_address, :delivery_address, :package_weight, :package_dimensions, :service_type, :estimated_cost, :notes)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':pickup_address', $full_pickup_address);
            $stmt->bindParam(':delivery_address', $full_delivery_address);
            $stmt->bindParam(':package_weight', $package_weight);
            $stmt->bindParam(':package_dimensions', $package_dimensions);
            $stmt->bindParam(':service_type', $service_type);
            $stmt->bindParam(':estimated_cost', $estimated_cost);
            $stmt->bindParam(':notes', $notes);
            
            if ($stmt->execute()) {
                $quote_submitted = true;
            } else {
                $error_message = 'There was an error submitting your quote. Please try again.';
            }
            
        } catch(PDOException $exception) {
            $error_message = 'Database error: ' . $exception->getMessage();
        }
    }
}

// Pre-select service if passed in URL
$selected_service = $_GET['service'] ?? '';
?>

<main>
    <section class="quote-page">
        <div class="container">
            <div class="page-header">
                <h1>Get Your Free Quote</h1>
                <p>Fill out the form below to get an instant shipping estimate. Our team will contact you with a detailed quote within 24 hours.</p>
            </div>
            
            <?php if ($quote_submitted): ?>
                <div class="quote-success">
                    <div class="success-content">
                        <i class="fas fa-check-circle"></i>
                        <h2>Quote Request Submitted!</h2>
                        <p>Thank you for your quote request. Here's your estimated shipping cost:</p>
                        <div class="estimated-cost">
                            <span class="cost-label">Estimated Cost:</span>
                            <span class="cost-amount">$<?php echo number_format($estimated_cost, 2); ?></span>
                        </div>
                        <p>Our team will contact you at <strong><?php echo htmlspecialchars($email); ?></strong> within 24 hours with a detailed quote and next steps.</p>
                        <div class="success-actions">
                            <a href="quote.php" class="btn btn-primary">Request Another Quote</a>
                            <a href="contact.php" class="btn btn-outline">Contact Us</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form class="quote-form" method="POST">
                    <div class="form-section">
                        <h2><i class="fas fa-user"></i> Contact Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2><i class="fas fa-map-marker-alt"></i> Pickup Information</h2>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="pickup_address">Pickup Address *</label>
                                <input type="text" id="pickup_address" name="pickup_address" value="<?php echo isset($_POST['pickup_address']) ? htmlspecialchars($_POST['pickup_address']) : ''; ?>" placeholder="Street address" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pickup_city">City *</label>
                                <input type="text" id="pickup_city" name="pickup_city" value="<?php echo isset($_POST['pickup_city']) ? htmlspecialchars($_POST['pickup_city']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="pickup_state">State *</label>
                                <select id="pickup_state" name="pickup_state" required>
                                    <option value="">Select State</option>
                                    <option value="AL" <?php echo (isset($_POST['pickup_state']) && $_POST['pickup_state'] === 'AL') ? 'selected' : ''; ?>>Alabama</option>
                                    <option value="CA" <?php echo (isset($_POST['pickup_state']) && $_POST['pickup_state'] === 'CA') ? 'selected' : ''; ?>>California</option>
                                    <option value="FL" <?php echo (isset($_POST['pickup_state']) && $_POST['pickup_state'] === 'FL') ? 'selected' : ''; ?>>Florida</option>
                                    <option value="NY" <?php echo (isset($_POST['pickup_state']) && $_POST['pickup_state'] === 'NY') ? 'selected' : ''; ?>>New York</option>
                                    <option value="TX" <?php echo (isset($_POST['pickup_state']) && $_POST['pickup_state'] === 'TX') ? 'selected' : ''; ?>>Texas</option>
                                    <!-- Add more states as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pickup_zip">ZIP Code *</label>
                                <input type="text" id="pickup_zip" name="pickup_zip" value="<?php echo isset($_POST['pickup_zip']) ? htmlspecialchars($_POST['pickup_zip']) : ''; ?>" pattern="[0-9]{5}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2><i class="fas fa-flag-checkered"></i> Delivery Information</h2>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="delivery_address">Delivery Address *</label>
                                <input type="text" id="delivery_address" name="delivery_address" value="<?php echo isset($_POST['delivery_address']) ? htmlspecialchars($_POST['delivery_address']) : ''; ?>" placeholder="Street address" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="delivery_city">City *</label>
                                <input type="text" id="delivery_city" name="delivery_city" value="<?php echo isset($_POST['delivery_city']) ? htmlspecialchars($_POST['delivery_city']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="delivery_state">State *</label>
                                <select id="delivery_state" name="delivery_state" required>
                                    <option value="">Select State</option>
                                    <option value="AL" <?php echo (isset($_POST['delivery_state']) && $_POST['delivery_state'] === 'AL') ? 'selected' : ''; ?>>Alabama</option>
                                    <option value="CA" <?php echo (isset($_POST['delivery_state']) && $_POST['delivery_state'] === 'CA') ? 'selected' : ''; ?>>California</option>
                                    <option value="FL" <?php echo (isset($_POST['delivery_state']) && $_POST['delivery_state'] === 'FL') ? 'selected' : ''; ?>>Florida</option>
                                    <option value="NY" <?php echo (isset($_POST['delivery_state']) && $_POST['delivery_state'] === 'NY') ? 'selected' : ''; ?>>New York</option>
                                    <option value="TX" <?php echo (isset($_POST['delivery_state']) && $_POST['delivery_state'] === 'TX') ? 'selected' : ''; ?>>Texas</option>
                                    <!-- Add more states as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="delivery_zip">ZIP Code *</label>
                                <input type="text" id="delivery_zip" name="delivery_zip" value="<?php echo isset($_POST['delivery_zip']) ? htmlspecialchars($_POST['delivery_zip']) : ''; ?>" pattern="[0-9]{5}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2><i class="fas fa-box"></i> Package Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="package_weight">Weight (lbs) *</label>
                                <input type="number" id="package_weight" name="package_weight" value="<?php echo isset($_POST['package_weight']) ? htmlspecialchars($_POST['package_weight']) : ''; ?>" min="0.1" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label for="package_length">Length (inches)</label>
                                <input type="number" id="package_length" name="package_length" value="<?php echo isset($_POST['package_length']) ? htmlspecialchars($_POST['package_length']) : ''; ?>" min="1">
                            </div>
                            <div class="form-group">
                                <label for="package_width">Width (inches)</label>
                                <input type="number" id="package_width" name="package_width" value="<?php echo isset($_POST['package_width']) ? htmlspecialchars($_POST['package_width']) : ''; ?>" min="1">
                            </div>
                            <div class="form-group">
                                <label for="package_height">Height (inches)</label>
                                <input type="number" id="package_height" name="package_height" value="<?php echo isset($_POST['package_height']) ? htmlspecialchars($_POST['package_height']) : ''; ?>" min="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2><i class="fas fa-shipping-fast"></i> Service Type</h2>
                        <div class="service-options">
                            <div class="service-option">
                                <input type="radio" id="standard" name="service_type" value="standard" <?php echo ($selected_service === 'standard' || (isset($_POST['service_type']) && $_POST['service_type'] === 'standard')) ? 'checked' : ''; ?> required>
                                <label for="standard" class="service-label">
                                    <div class="service-info">
                                        <h3><i class="fas fa-shipping-fast"></i> Standard Shipping</h3>
                                        <p>2-5 business days - Starting at $8.99</p>
                                    </div>
                                </label>
                            </div>
                            <div class="service-option">
                                <input type="radio" id="express" name="service_type" value="express" <?php echo ($selected_service === 'express' || (isset($_POST['service_type']) && $_POST['service_type'] === 'express')) ? 'checked' : ''; ?>>
                                <label for="express" class="service-label">
                                    <div class="service-info">
                                        <h3><i class="fas fa-bolt"></i> Express Delivery</h3>
                                        <p>Next business day - Starting at $24.99</p>
                                    </div>
                                </label>
                            </div>
                            <div class="service-option">
                                <input type="radio" id="same_day" name="service_type" value="same_day" <?php echo ($selected_service === 'same_day' || (isset($_POST['service_type']) && $_POST['service_type'] === 'same_day')) ? 'checked' : ''; ?>>
                                <label for="same_day" class="service-label">
                                    <div class="service-info">
                                        <h3><i class="fas fa-clock"></i> Same Day Delivery</h3>
                                        <p>Within 6 hours - Starting at $39.99</p>
                                    </div>
                                </label>
                            </div>
                            <div class="service-option">
                                <input type="radio" id="overnight" name="service_type" value="overnight" <?php echo ($selected_service === 'overnight' || (isset($_POST['service_type']) && $_POST['service_type'] === 'overnight')) ? 'checked' : ''; ?>>
                                <label for="overnight" class="service-label">
                                    <div class="service-info">
                                        <h3><i class="fas fa-moon"></i> Overnight Delivery</h3>
                                        <p>By 10:30 AM next day - Starting at $29.99</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2><i class="fas fa-comment"></i> Additional Notes</h2>
                        <div class="form-group">
                            <label for="notes">Special Instructions</label>
                            <textarea id="notes" name="notes" rows="4" placeholder="Any special handling requirements, delivery instructions, or additional information..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-calculator"></i> Get Free Quote
                        </button>
                        <p class="form-disclaimer">
                            By submitting this form, you agree to receive a quote and communication from SwiftDelivery. 
                            Your information will be kept confidential and never shared with third parties.
                        </p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>