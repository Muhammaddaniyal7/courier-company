<?php
$page_title = "Pricing";
include 'includes/header.php';
?>

<main>
    <section class="pricing-page">
        <div class="container">
            <div class="page-header">
                <h1>Transparent Pricing</h1>
                <p>Simple, competitive rates with no hidden fees. Choose the service that best fits your delivery needs and budget.</p>
            </div>
            
            <!-- Pricing Tables -->
            <div class="pricing-tables">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <i class="fas fa-shipping-fast"></i>
                        <h3>Standard Shipping</h3>
                        <p class="delivery-time">2-5 Business Days</p>
                    </div>
                    <div class="pricing-body">
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">8.99</span>
                            <span class="period">starting</span>
                        </div>
                        <ul class="features">
                            <li><i class="fas fa-check"></i> Packages up to 50 lbs</li>
                            <li><i class="fas fa-check"></i> Online tracking</li>
                            <li><i class="fas fa-check"></i> Email notifications</li>
                            <li><i class="fas fa-check"></i> $500 insurance included</li>
                            <li><i class="fas fa-check"></i> Proof of delivery</li>
                        </ul>
                        <div class="weight-pricing">
                            <h4>Weight-based Pricing:</h4>
                            <div class="price-row">
                                <span>Up to 1 lb</span>
                                <span>$8.99</span>
                            </div>
                            <div class="price-row">
                                <span>1-5 lbs</span>
                                <span>$12.99</span>
                            </div>
                            <div class="price-row">
                                <span>5-10 lbs</span>
                                <span>$16.99</span>
                            </div>
                            <div class="price-row">
                                <span>10-20 lbs</span>
                                <span>$22.99</span>
                            </div>
                            <div class="price-row">
                                <span>20-50 lbs</span>
                                <span>$29.99</span>
                            </div>
                        </div>
                        <a href="quote.php?service=standard" class="btn btn-primary">Get Quote</a>
                    </div>
                </div>
                
                <div class="pricing-card featured">
                    <div class="popular-badge">Most Popular</div>
                    <div class="pricing-header">
                        <i class="fas fa-bolt"></i>
                        <h3>Express Delivery</h3>
                        <p class="delivery-time">Next Business Day</p>
                    </div>
                    <div class="pricing-body">
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">24.99</span>
                            <span class="period">starting</span>
                        </div>
                        <ul class="features">
                            <li><i class="fas fa-check"></i> Packages up to 50 lbs</li>
                            <li><i class="fas fa-check"></i> Real-time GPS tracking</li>
                            <li><i class="fas fa-check"></i> Priority handling</li>
                            <li><i class="fas fa-check"></i> $1000 insurance included</li>
                            <li><i class="fas fa-check"></i> Weekend delivery available</li>
                        </ul>
                        <div class="weight-pricing">
                            <h4>Weight-based Pricing:</h4>
                            <div class="price-row">
                                <span>Up to 1 lb</span>
                                <span>$24.99</span>
                            </div>
                            <div class="price-row">
                                <span>1-5 lbs</span>
                                <span>$29.99</span>
                            </div>
                            <div class="price-row">
                                <span>5-10 lbs</span>
                                <span>$34.99</span>
                            </div>
                            <div class="price-row">
                                <span>10-20 lbs</span>
                                <span>$42.99</span>
                            </div>
                            <div class="price-row">
                                <span>20-50 lbs</span>
                                <span>$54.99</span>
                            </div>
                        </div>
                        <a href="quote.php?service=express" class="btn btn-primary">Get Quote</a>
                    </div>
                </div>
                
                <div class="pricing-card">
                    <div class="pricing-header">
                        <i class="fas fa-clock"></i>
                        <h3>Same Day Delivery</h3>
                        <p class="delivery-time">Within 6 Hours</p>
                    </div>
                    <div class="pricing-body">
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">39.99</span>
                            <span class="period">starting</span>
                        </div>
                        <ul class="features">
                            <li><i class="fas fa-check"></i> Packages up to 25 lbs</li>
                            <li><i class="fas fa-check"></i> Live GPS tracking</li>
                            <li><i class="fas fa-check"></i> Dedicated courier</li>
                            <li><i class="fas fa-check"></i> $1000 insurance included</li>
                            <li><i class="fas fa-check"></i> Photo proof of delivery</li>
                        </ul>
                        <div class="weight-pricing">
                            <h4>Weight-based Pricing:</h4>
                            <div class="price-row">
                                <span>Up to 1 lb</span>
                                <span>$39.99</span>
                            </div>
                            <div class="price-row">
                                <span>1-5 lbs</span>
                                <span>$49.99</span>
                            </div>
                            <div class="price-row">
                                <span>5-10 lbs</span>
                                <span>$59.99</span>
                            </div>
                            <div class="price-row">
                                <span>10-25 lbs</span>
                                <span>$74.99</span>
                            </div>
                        </div>
                        <a href="quote.php?service=same_day" class="btn btn-primary">Get Quote</a>
                    </div>
                </div>
            </div>
            
            <!-- International Pricing -->
            <div class="international-pricing">
                <h2>International Shipping</h2>
                <p>Worldwide delivery with competitive international rates</p>
                
                <div class="international-tables">
                    <div class="zone-table">
                        <h3>Zone 1 - Canada & Mexico</h3>
                        <div class="zone-pricing">
                            <div class="price-row header">
                                <span>Weight</span>
                                <span>Express (2-3 days)</span>
                                <span>Standard (5-7 days)</span>
                            </div>
                            <div class="price-row">
                                <span>Up to 1 lb</span>
                                <span>$29.99</span>
                                <span>$19.99</span>
                            </div>
                            <div class="price-row">
                                <span>1-5 lbs</span>
                                <span>$39.99</span>
                                <span>$24.99</span>
                            </div>
                            <div class="price-row">
                                <span>5-10 lbs</span>
                                <span>$54.99</span>
                                <span>$34.99</span>
                            </div>
                            <div class="price-row">
                                <span>10-20 lbs</span>
                                <span>$74.99</span>
                                <span>$49.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="zone-table">
                        <h3>Zone 2 - Europe & UK</h3>
                        <div class="zone-pricing">
                            <div class="price-row header">
                                <span>Weight</span>
                                <span>Express (3-5 days)</span>
                                <span>Standard (7-10 days)</span>
                            </div>
                            <div class="price-row">
                                <span>Up to 1 lb</span>
                                <span>$39.99</span>
                                <span>$29.99</span>
                            </div>
                            <div class="price-row">
                                <span>1-5 lbs</span>
                                <span>$54.99</span>
                                <span>$39.99</span>
                            </div>
                            <div class="price-row">
                                <span>5-10 lbs</span>
                                <span>$74.99</span>
                                <span>$54.99</span>
                            </div>
                            <div class="price-row">
                                <span>10-20 lbs</span>
                                <span>$99.99</span>
                                <span>$74.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="zone-table">
                        <h3>Zone 3 - Asia Pacific</h3>
                        <div class="zone-pricing">
                            <div class="price-row header">
                                <span>Weight</span>
                                <span>Express (3-5 days)</span>
                                <span>Standard (10-14 days)</span>
                            </div>
                            <div class="price-row">
                                <span>Up to 1 lb</span>
                                <span>$49.99</span>
                                <span>$34.99</span>
                            </div>
                            <div class="price-row">
                                <span>1-5 lbs</span>
                                <span>$69.99</span>
                                <span>$49.99</span>
                            </div>
                            <div class="price-row">
                                <span>5-10 lbs</span>
                                <span>$94.99</span>
                                <span>$69.99</span>
                            </div>
                            <div class="price-row">
                                <span>10-20 lbs</span>
                                <span>$129.99</span>
                                <span>$94.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Fees -->
            <div class="additional-fees">
                <h2>Additional Services & Fees</h2>
                <div class="fees-grid">
                    <div class="fee-category">
                        <h3>Optional Services</h3>
                        <div class="fee-list">
                            <div class="fee-item">
                                <span>Signature Required</span>
                                <span>$3.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Adult Signature</span>
                                <span>$5.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Saturday Delivery</span>
                                <span>$12.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Sunday Delivery</span>
                                <span>$19.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Holiday Delivery</span>
                                <span>$24.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fee-category">
                        <h3>Special Handling</h3>
                        <div class="fee-list">
                            <div class="fee-item">
                                <span>Fragile Handling</span>
                                <span>$7.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Oversized Package</span>
                                <span>$15.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Temperature Controlled</span>
                                <span>$24.99</span>
                            </div>
                            <div class="fee-item">
                                <span>White Glove Service</span>
                                <span>$49.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fee-category">
                        <h3>Insurance Options</h3>
                        <div class="fee-list">
                            <div class="fee-item">
                                <span>Additional Insurance (per $100)</span>
                                <span>$1.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Declared Value up to $2,500</span>
                                <span>$9.99</span>
                            </div>
                            <div class="fee-item">
                                <span>Declared Value up to $5,000</span>
                                <span>$19.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Volume Discounts -->
            <div class="volume-discounts">
                <h2>Volume Discounts</h2>
                <p>Save more with higher shipping volumes. Perfect for businesses with regular shipping needs.</p>
                <div class="discount-tiers">
                    <div class="tier">
                        <h3>Bronze Tier</h3>
                        <p>10-49 shipments/month</p>
                        <div class="discount">5% discount</div>
                    </div>
                    <div class="tier">
                        <h3>Silver Tier</h3>
                        <p>50-199 shipments/month</p>
                        <div class="discount">10% discount</div>
                    </div>
                    <div class="tier">
                        <h3>Gold Tier</h3>
                        <p>200-499 shipments/month</p>
                        <div class="discount">15% discount</div>
                    </div>
                    <div class="tier">
                        <h3>Platinum Tier</h3>
                        <p>500+ shipments/month</p>
                        <div class="discount">20% discount</div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="pricing-cta">
                <h2>Need a Custom Quote?</h2>
                <p>For bulk shipments, specialized services, or enterprise solutions, contact us for personalized pricing.</p>
                <div class="cta-buttons">
                    <a href="quote.php" class="btn btn-primary">Get Custom Quote</a>
                    <a href="contact.php" class="btn btn-outline">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>