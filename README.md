# SwiftDelivery - Courier Company Website

A modern, professional courier company website built with PHP, featuring a comprehensive tracking system, quote requests, and responsive design.

## 🚀 Features

### Core Functionality
- **Package Tracking**: Real-time tracking with detailed shipment history
- **Quote System**: Instant shipping estimates with automated pricing
- **Service Pages**: Detailed information about all courier services
- **Contact Forms**: Multiple contact methods with form validation
- **Responsive Design**: Mobile-first approach with modern UI/UX

### Services Offered
- Express Delivery (24-hour delivery)
- Standard Shipping (2-5 business days)
- Same Day Delivery (within 6 hours)
- Overnight Delivery (next business day)
- International Shipping (worldwide coverage)

### Technical Features
- **Database Integration**: MySQL with PDO for secure data handling
- **Modern PHP**: Object-oriented design with prepared statements
- **Responsive CSS**: Mobile-first design with CSS Grid and Flexbox
- **Interactive JavaScript**: Form validation, animations, and user experience enhancements
- **Professional UI**: Clean, modern design with Font Awesome icons

## 📁 Project Structure

```
/workspace/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet with responsive design
│   └── js/
│       └── script.js          # JavaScript functionality
├── config/
│   └── database.php           # Database connection configuration
├── database/
│   └── schema.sql             # Database schema and sample data
├── includes/
│   ├── header.php             # Header with navigation
│   └── footer.php             # Footer with company information
├── index.php                  # Homepage
├── tracking.php               # Package tracking page
├── services.php               # Services information page
├── pricing.php                # Pricing tables and information
├── contact.php                # Contact form and information
├── quote.php                  # Quote request form
└── README.md                  # Project documentation
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Database Setup
1. Create a MySQL database named `courier_db`
2. Import the database schema:
   ```sql
   mysql -u your_username -p courier_db < database/schema.sql
   ```

### Configuration
1. Update database credentials in `config/database.php`:
   ```php
   private $host = 'localhost';
   private $db_name = 'courier_db';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

### Web Server Setup
1. Place files in your web server's document root
2. Ensure PHP has write permissions for form submissions
3. Enable URL rewriting if needed

## 📊 Database Schema

### Tables
- **customers**: Customer information and contact details
- **shipments**: Package shipment records with tracking
- **tracking_events**: Detailed tracking history for each shipment
- **quotes**: Quote requests from customers
- **admin_users**: Administrative user accounts

### Sample Data
The schema includes sample data for testing:
- 3 sample customers
- 3 sample shipments with different statuses
- Tracking events for shipment history
- Default admin user (username: `admin`, password: `admin123`)

## 🎯 Usage

### For Customers
1. **Track Packages**: Enter tracking number on homepage or tracking page
2. **Request Quote**: Fill out detailed quote form with package information
3. **Browse Services**: View detailed service descriptions and pricing
4. **Contact Support**: Multiple contact methods with business hours

### Sample Tracking Numbers
- `SD001234567890` - Express delivery (In transit)
- `SD001234567891` - Standard shipping (Picked up)
- `SD001234567892` - Overnight delivery (Delivered)

### For Development
- All forms include client-side validation
- Database queries use prepared statements for security
- Responsive design works on all device sizes
- Professional error handling and user feedback

## 🎨 Design Features

### Visual Design
- Modern gradient backgrounds
- Professional color scheme (Blue #2563eb primary)
- Clean typography with Inter font family
- Consistent spacing and layout grid

### User Experience
- Intuitive navigation with mobile hamburger menu
- Interactive form elements with real-time validation
- Smooth animations and hover effects
- Loading states and user feedback

### Responsive Design
- Mobile-first approach
- CSS Grid and Flexbox layouts
- Adaptive navigation for mobile devices
- Optimized for all screen sizes

## 🔧 Customization

### Branding
- Update company name in `includes/header.php` and `includes/footer.php`
- Modify color scheme in `assets/css/style.css`
- Replace contact information throughout the site

### Services & Pricing
- Edit service descriptions in `services.php`
- Update pricing tables in `pricing.php`
- Modify quote calculation logic in `quote.php`

### Database Configuration
- Adjust table structure in `database/schema.sql`
- Update queries in PHP files as needed
- Add new tracking statuses or service types

## 🔒 Security Features

- **SQL Injection Protection**: All queries use PDO prepared statements
- **Input Validation**: Server-side and client-side form validation
- **Data Sanitization**: All user input is properly sanitized
- **Error Handling**: Professional error messages without exposing system details

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the MIT License.

## 📞 Support

For technical support or questions:
- Email: developer@swiftdelivery.com
- Documentation: See inline code comments
- Issues: Submit via GitHub issues

## 🚀 Deployment

### Production Checklist
- [ ] Update database credentials
- [ ] Enable error logging
- [ ] Configure SSL certificate
- [ ] Set up regular database backups
- [ ] Configure email sending for contact forms
- [ ] Test all functionality in production environment

---

**SwiftDelivery** - Professional courier services with modern web technology.