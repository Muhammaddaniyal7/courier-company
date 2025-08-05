<?php
$page_title = "Home";
include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Fast, Reliable Courier Services</h1>
                <p>Your trusted partner for express delivery worldwide. We ensure your packages reach their destination safely and on time.</p>
                <div class="hero-buttons">
                    <a href="quote.php" class="btn btn-primary">Get Quote</a>
                    <a href="tracking.php" class="btn btn-secondary">Track Package</a>
                </div>
            </div>
            <div class="hero-image">
                <i class="fas fa-truck hero-icon"></i>
            </div>
        </div>
    </section>

    <!-- Quick Tracking Section -->
    <section class="quick-tracking">
        <div class="container">
            <div class="tracking-box">
                <h2>Track Your Package</h2>
                <form class="tracking-form" action="tracking.php" method="GET">
                    <div class="form-group">
                        <input type="text" name="tracking_number" placeholder="Enter tracking number (e.g., SD001234567890)" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Track
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="section-header">
                <h2>Our Services</h2>
                <p>We offer a comprehensive range of courier and delivery services to meet all your shipping needs.</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Express Delivery</h3>
                    <p>Ultra-fast delivery for urgent packages. Get your items delivered within 24 hours.</p>
                    <ul>
                        <li>24-hour delivery</li>
                        <li>Real-time tracking</li>
                        <li>Priority handling</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Standard Shipping</h3>
                    <p>Reliable and cost-effective shipping for everyday deliveries. 2-5 business days.</p>
                    <ul>
                        <li>2-5 business days</li>
                        <li>Affordable rates</li>
                        <li>Secure packaging</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>International</h3>
                    <p>Worldwide shipping with customs handling and international tracking.</p>
                    <ul>
                        <li>Global coverage</li>
                        <li>Customs clearance</li>
                        <li>Door-to-door service</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Same Day Delivery</h3>
                    <p>Need it today? Our same-day delivery service ensures your package arrives within hours.</p>
                    <ul>
                        <li>Within 6 hours</li>
                        <li>Local deliveries</li>
                        <li>Emergency service</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose SwiftDelivery?</h2>
                <p>We're committed to providing the best courier experience with innovative features and reliable service.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Real-Time Tracking</h3>
                    <p>Monitor your package journey with live GPS tracking and instant status updates.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Insured</h3>
                    <p>All packages are fully insured and handled with the highest security standards.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Round-the-clock customer support to assist you whenever you need help.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Competitive Rates</h3>
                    <p>Get the best value for your money with our transparent and competitive pricing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">Packages Delivered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.8%</div>
                    <div class="stat-label">On-Time Delivery</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Cities Covered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Customer Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Ship Your Package?</h2>
                <p>Get started with SwiftDelivery today and experience the difference of professional courier services.</p>
                <div class="cta-buttons">
                    <a href="quote.php" class="btn btn-primary">Get Free Quote</a>
                    <a href="contact.php" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>