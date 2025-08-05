// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Close menu when clicking on nav links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!hamburger.contains(event.target) && !navMenu.contains(event.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation enhancement
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        const errorElement = input.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
        
        if (!input.value.trim()) {
            showError(input, 'This field is required');
            isValid = false;
        } else if (input.type === 'email' && !isValidEmail(input.value)) {
            showError(input, 'Please enter a valid email address');
            isValid = false;
        } else if (input.type === 'tel' && input.value && !isValidPhone(input.value)) {
            showError(input, 'Please enter a valid phone number');
            isValid = false;
        }
    });
    
    return isValid;
}

function showError(input, message) {
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.style.color = '#dc2626';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '0.25rem';
    errorElement.textContent = message;
    input.parentNode.appendChild(errorElement);
    input.style.borderColor = '#dc2626';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/\D/g, ''));
}

// Real-time form validation
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            const errorElement = this.parentNode.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
            
            if (this.hasAttribute('required') && !this.value.trim()) {
                showError(this, 'This field is required');
            } else if (this.type === 'email' && this.value && !isValidEmail(this.value)) {
                showError(this, 'Please enter a valid email address');
            } else {
                this.style.borderColor = '#10b981';
            }
        });
        
        input.addEventListener('focus', function() {
            this.style.borderColor = '#2563eb';
        });
    });
});

// Tracking number formatter
document.addEventListener('DOMContentLoaded', function() {
    const trackingInputs = document.querySelectorAll('input[name="tracking_number"]');
    
    trackingInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Convert to uppercase and remove non-alphanumeric characters
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    });
});

// Auto-calculate shipping estimate (for quote form)
document.addEventListener('DOMContentLoaded', function() {
    const quoteForm = document.querySelector('.quote-form');
    if (quoteForm) {
        const weightInput = quoteForm.querySelector('input[name="package_weight"]');
        const serviceInputs = quoteForm.querySelectorAll('input[name="service_type"]');
        const pickupState = quoteForm.querySelector('select[name="pickup_state"]');
        const deliveryState = quoteForm.querySelector('select[name="delivery_state"]');
        
        function calculateEstimate() {
            const weight = parseFloat(weightInput?.value) || 0;
            const selectedService = quoteForm.querySelector('input[name="service_type"]:checked');
            
            if (weight > 0 && selectedService) {
                const serviceType = selectedService.value;
                const baseRates = {
                    'standard': 8.99,
                    'express': 24.99,
                    'same_day': 39.99,
                    'overnight': 29.99
                };
                
                let weightMultiplier = 1;
                if (weight > 1) weightMultiplier = 1.5;
                if (weight > 5) weightMultiplier = 2;
                if (weight > 10) weightMultiplier = 2.5;
                if (weight > 20) weightMultiplier = 3;
                
                const pickup = pickupState?.value || '';
                const delivery = deliveryState?.value || '';
                const distanceMultiplier = (pickup && delivery && pickup !== delivery) ? 1.5 : 1;
                
                const estimate = Math.round(baseRates[serviceType] * weightMultiplier * distanceMultiplier * 100) / 100;
                
                // Display estimate (you could add a display element for this)
                console.log(`Estimated cost: $${estimate.toFixed(2)}`);
            }
        }
        
        if (weightInput) weightInput.addEventListener('input', calculateEstimate);
        serviceInputs.forEach(input => input.addEventListener('change', calculateEstimate));
        if (pickupState) pickupState.addEventListener('change', calculateEstimate);
        if (deliveryState) deliveryState.addEventListener('change', calculateEstimate);
    }
});

// Animate numbers on scroll (for stats section)
function animateNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = target.textContent.replace(/[^\d.]/g, '');
                const isPercentage = target.textContent.includes('%');
                const isPlus = target.textContent.includes('+');
                const duration = 2000;
                const steps = 60;
                const stepValue = parseFloat(finalValue) / steps;
                let currentValue = 0;
                
                const timer = setInterval(() => {
                    currentValue += stepValue;
                    if (currentValue >= parseFloat(finalValue)) {
                        currentValue = parseFloat(finalValue);
                        clearInterval(timer);
                    }
                    
                    let displayValue = Math.floor(currentValue);
                    if (finalValue.includes('.')) {
                        displayValue = currentValue.toFixed(1);
                    }
                    
                    target.textContent = displayValue + (isPercentage ? '%' : '') + (isPlus ? '+' : '');
                }, duration / steps);
                
                observer.unobserve(target);
            }
        });
    }, { threshold: 0.5 });
    
    statNumbers.forEach(number => observer.observe(number));
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', animateNumbers);

// Service card hover effects
document.addEventListener('DOMContentLoaded', function() {
    const serviceCards = document.querySelectorAll('.service-card, .pricing-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Copy tracking number functionality
document.addEventListener('DOMContentLoaded', function() {
    const sampleNumbers = document.querySelectorAll('.sample-item code');
    
    sampleNumbers.forEach(code => {
        code.style.cursor = 'pointer';
        code.title = 'Click to copy';
        
        code.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                this.style.color = '#10b981';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.color = '#2563eb';
                }, 1000);
            });
        });
    });
});

// Form auto-save (for quote form)
document.addEventListener('DOMContentLoaded', function() {
    const quoteForm = document.querySelector('.quote-form');
    if (quoteForm) {
        const inputs = quoteForm.querySelectorAll('input, select, textarea');
        
        // Load saved data
        inputs.forEach(input => {
            const savedValue = localStorage.getItem(`quote_${input.name}`);
            if (savedValue && !input.value) {
                input.value = savedValue;
                if (input.type === 'radio' && input.value === savedValue) {
                    input.checked = true;
                }
            }
        });
        
        // Save data on change
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.type === 'radio') {
                    if (this.checked) {
                        localStorage.setItem(`quote_${this.name}`, this.value);
                    }
                } else {
                    localStorage.setItem(`quote_${this.name}`, this.value);
                }
            });
        });
        
        // Clear saved data on successful submission
        quoteForm.addEventListener('submit', function() {
            inputs.forEach(input => {
                localStorage.removeItem(`quote_${input.name}`);
            });
        });
    }
});

// Progressive enhancement for forms
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const submitButton = form.querySelector('button[type="submit"]');
        
        if (submitButton) {
            form.addEventListener('submit', function(e) {
                // Add loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // If validation fails, restore button
                if (!validateForm(form)) {
                    e.preventDefault();
                    submitButton.disabled = false;
                    submitButton.innerHTML = submitButton.dataset.originalText || 'Submit';
                } else {
                    // Store original text for later restoration
                    submitButton.dataset.originalText = submitButton.innerHTML;
                }
            });
        }
    });
});

// Lazy loading for images (if any are added later)
document.addEventListener('DOMContentLoaded', function() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// Scroll to top functionality
document.addEventListener('DOMContentLoaded', function() {
    // Create scroll to top button
    const scrollButton = document.createElement('button');
    scrollButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollButton.className = 'scroll-to-top';
    scrollButton.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
    `;
    
    document.body.appendChild(scrollButton);
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollButton.style.opacity = '1';
            scrollButton.style.visibility = 'visible';
        } else {
            scrollButton.style.opacity = '0';
            scrollButton.style.visibility = 'hidden';
        }
    });
    
    // Scroll to top when clicked
    scrollButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

// Enhanced hamburger animation
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            const bars = this.querySelectorAll('.bar');
            
            if (this.classList.contains('active')) {
                // Animate to X
                bars[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                bars[1].style.opacity = '0';
                bars[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
            } else {
                // Animate back to hamburger
                bars[0].style.transform = 'none';
                bars[1].style.opacity = '1';
                bars[2].style.transform = 'none';
            }
        });
    }
});

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Utility function to format phone numbers
function formatPhoneNumber(phone) {
    const cleaned = phone.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
    if (match) {
        return '(' + match[1] + ') ' + match[2] + '-' + match[3];
    }
    return phone;
}

// Add phone number formatting to phone inputs
document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatPhoneNumber(this.value);
        });
    });
});

console.log('SwiftDelivery website loaded successfully!');