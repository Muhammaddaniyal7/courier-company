<?php
$page_title = "Contact Us";
include 'includes/header.php';

$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // In a real application, you would send an email here
        // For demo purposes, we'll just show a success message
        $message_sent = true;
    }
}
?>

<main>
    <section class="contact-page">
        <div class="container">
            <div class="page-header">
                <h1>Contact Us</h1>
                <p>Get in touch with our team for quotes, support, or any questions about our courier services.</p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>We're here to help! Reach out to us through any of the following methods:</p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="method-info">
                                <h3>Phone</h3>
                                <p>+1 (555) 123-4567</p>
                                <span>Mon-Fri: 8AM-6PM, Sat: 9AM-4PM</span>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="method-info">
                                <h3>Email</h3>
                                <p>info@swiftdelivery.com</p>
                                <span>We respond within 24 hours</span>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="method-info">
                                <h3>Address</h3>
                                <p>123 Business Avenue<br>
                                Suite 100<br>
                                City, State 12345</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="method-info">
                                <h3>24/7 Support</h3>
                                <p>+1 (555) 123-HELP</p>
                                <span>Emergency and after-hours support</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="business-hours">
                        <h3>Business Hours</h3>
                        <div class="hours-list">
                            <div class="hours-item">
                                <span>Monday - Friday</span>
                                <span>8:00 AM - 6:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span>Saturday</span>
                                <span>9:00 AM - 4:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span>Sunday</span>
                                <span>Emergency service only</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form-section">
                    <h2>Send us a Message</h2>
                    
                    <?php if ($message_sent): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Thank you for your message! We'll get back to you within 24 hours.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form class="contact-form" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="general" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="quote" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'quote') ? 'selected' : ''; ?>>Request Quote</option>
                                    <option value="support" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'support') ? 'selected' : ''; ?>>Customer Support</option>
                                    <option value="business" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'business') ? 'selected' : ''; ?>>Business Account</option>
                                    <option value="complaint" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'complaint') ? 'selected' : ''; ?>>Complaint</option>
                                    <option value="other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="6" placeholder="Please provide details about your inquiry..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="faq-section">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-grid">
                    <div class="faq-item">
                        <h3>How can I track my package?</h3>
                        <p>You can track your package using our online tracking system. Simply enter your tracking number on our tracking page or homepage.</p>
                    </div>
                    <div class="faq-item">
                        <h3>What are your delivery times?</h3>
                        <p>Delivery times vary by service: Same Day (6 hours), Express (next business day), Standard (2-5 business days), and International (varies by destination).</p>
                    </div>
                    <div class="faq-item">
                        <h3>Do you offer insurance?</h3>
                        <p>Yes, all our services include basic insurance coverage. Additional insurance is available for high-value items.</p>
                    </div>
                    <div class="faq-item">
                        <h3>Can I schedule a pickup?</h3>
                        <p>Absolutely! You can schedule pickups online or by calling our customer service team. Same-day pickup is available in most areas.</p>
                    </div>
                    <div class="faq-item">
                        <h3>What payment methods do you accept?</h3>
                        <p>We accept all major credit cards, PayPal, bank transfers, and offer billing accounts for business customers.</p>
                    </div>
                    <div class="faq-item">
                        <h3>Do you ship internationally?</h3>
                        <p>Yes, we ship to over 220 countries worldwide with full tracking and customs assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>