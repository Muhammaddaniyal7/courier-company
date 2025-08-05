<?php
$page_title = "Services";
include 'includes/header.php';
?>

<main>
    <section class="services-page">
        <div class="container">
            <div class="page-header">
                <h1>Our Courier Services</h1>
                <p>Professional delivery solutions tailored to meet your specific shipping needs. From express delivery to international shipping, we've got you covered.</p>
            </div>
            
            <!-- Services Grid -->
            <div class="services-detailed">
                <div class="service-detail">
                    <div class="service-header">
                        <div class="service-icon large">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="service-title">
                            <h2>Express Delivery</h2>
                            <p class="subtitle">When time is critical</p>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="service-description">
                            <p>Our express delivery service ensures your urgent packages reach their destination within 24 hours. Perfect for time-sensitive documents, medical supplies, and emergency deliveries.</p>
                        </div>
                        <div class="service-features">
                            <h4>Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> 24-hour guaranteed delivery</li>
                                <li><i class="fas fa-check"></i> Real-time GPS tracking</li>
                                <li><i class="fas fa-check"></i> Priority handling and sorting</li>
                                <li><i class="fas fa-check"></i> Proof of delivery</li>
                                <li><i class="fas fa-check"></i> Insurance coverage up to $1000</li>
                                <li><i class="fas fa-check"></i> Weekend and holiday delivery available</li>
                            </ul>
                        </div>
                        <div class="service-pricing">
                            <h4>Starting at $24.99</h4>
                            <a href="quote.php?service=express" class="btn btn-primary">Get Quote</a>
                        </div>
                    </div>
                </div>
                
                <div class="service-detail">
                    <div class="service-header">
                        <div class="service-icon large">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="service-title">
                            <h2>Standard Shipping</h2>
                            <p class="subtitle">Reliable and affordable</p>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="service-description">
                            <p>Our most popular service offering reliable delivery at competitive rates. Perfect for regular business shipments and personal packages that don't require express handling.</p>
                        </div>
                        <div class="service-features">
                            <h4>Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> 2-5 business days delivery</li>
                                <li><i class="fas fa-check"></i> Online tracking system</li>
                                <li><i class="fas fa-check"></i> Secure packaging handling</li>
                                <li><i class="fas fa-check"></i> Email notifications</li>
                                <li><i class="fas fa-check"></i> Insurance coverage up to $500</li>
                                <li><i class="fas fa-check"></i> Bulk shipping discounts</li>
                            </ul>
                        </div>
                        <div class="service-pricing">
                            <h4>Starting at $8.99</h4>
                            <a href="quote.php?service=standard" class="btn btn-primary">Get Quote</a>
                        </div>
                    </div>
                </div>
                
                <div class="service-detail">
                    <div class="service-header">
                        <div class="service-icon large">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="service-title">
                            <h2>International Shipping</h2>
                            <p class="subtitle">Worldwide delivery</p>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="service-description">
                            <p>Ship your packages anywhere in the world with our comprehensive international delivery service. We handle all customs documentation and provide end-to-end tracking.</p>
                        </div>
                        <div class="service-features">
                            <h4>Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Worldwide delivery coverage</li>
                                <li><i class="fas fa-check"></i> Customs clearance assistance</li>
                                <li><i class="fas fa-check"></i> International tracking</li>
                                <li><i class="fas fa-check"></i> Documentation support</li>
                                <li><i class="fas fa-check"></i> Multiple service speed options</li>
                                <li><i class="fas fa-check"></i> Duty and tax calculation</li>
                            </ul>
                        </div>
                        <div class="service-pricing">
                            <h4>Starting at $29.99</h4>
                            <a href="quote.php?service=international" class="btn btn-primary">Get Quote</a>
                        </div>
                    </div>
                </div>
                
                <div class="service-detail">
                    <div class="service-header">
                        <div class="service-icon large">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="service-title">
                            <h2>Same Day Delivery</h2>
                            <p class="subtitle">Ultra-fast local delivery</p>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="service-description">
                            <p>Need it delivered today? Our same-day delivery service provides ultra-fast delivery within major metropolitan areas. Perfect for emergency deliveries and last-minute needs.</p>
                        </div>
                        <div class="service-features">
                            <h4>Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Delivery within 6 hours</li>
                                <li><i class="fas fa-check"></i> Live tracking and updates</li>
                                <li><i class="fas fa-check"></i> Dedicated courier assignment</li>
                                <li><i class="fas fa-check"></i> Direct delivery route</li>
                                <li><i class="fas fa-check"></i> Emergency service availability</li>
                                <li><i class="fas fa-check"></i> Photo proof of delivery</li>
                            </ul>
                        </div>
                        <div class="service-pricing">
                            <h4>Starting at $39.99</h4>
                            <a href="quote.php?service=same_day" class="btn btn-primary">Get Quote</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Services -->
            <div class="additional-services">
                <h2>Additional Services</h2>
                <div class="additional-grid">
                    <div class="additional-item">
                        <i class="fas fa-boxes"></i>
                        <h3>Bulk Shipping</h3>
                        <p>Special rates for businesses with high volume shipping needs. Contact us for custom pricing.</p>
                    </div>
                    <div class="additional-item">
                        <i class="fas fa-warehouse"></i>
                        <h3>Warehousing</h3>
                        <p>Secure storage solutions for your inventory with flexible pickup and delivery options.</p>
                    </div>
                    <div class="additional-item">
                        <i class="fas fa-handshake"></i>
                        <h3>White Glove Service</h3>
                        <p>Premium handling service for fragile, valuable, or oversized items requiring special care.</p>
                    </div>
                    <div class="additional-item">
                        <i class="fas fa-redo"></i>
                        <h3>Return Services</h3>
                        <p>Streamlined return processing for e-commerce businesses and customer returns.</p>
                    </div>
                </div>
            </div>
            
            <!-- Service Areas -->
            <div class="service-areas">
                <h2>Service Coverage</h2>
                <div class="coverage-info">
                    <div class="coverage-item">
                        <h3><i class="fas fa-map-marker-alt"></i> Local Delivery</h3>
                        <p>Same-day and next-day delivery available in major metropolitan areas including New York, Los Angeles, Chicago, Houston, Phoenix, Philadelphia, San Antonio, San Diego, Dallas, and San Jose.</p>
                    </div>
                    <div class="coverage-item">
                        <h3><i class="fas fa-flag-usa"></i> Nationwide Shipping</h3>
                        <p>Standard and express delivery to all 50 states, including Alaska and Hawaii. Remote area delivery available with extended transit times.</p>
                    </div>
                    <div class="coverage-item">
                        <h3><i class="fas fa-globe-americas"></i> International Coverage</h3>
                        <p>Worldwide shipping to over 220 countries and territories. Express international service available to major global cities.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>