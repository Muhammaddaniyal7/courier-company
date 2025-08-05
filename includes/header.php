<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>SwiftDelivery Courier Services</title>
    <link rel="stylesheet" href="/workspace/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="/workspace/index.php">
                        <i class="fas fa-shipping-fast"></i>
                        <span>SwiftDelivery</span>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="/workspace/index.php" class="nav-link">Home</a></li>
                    <li><a href="/workspace/services.php" class="nav-link">Services</a></li>
                    <li><a href="/workspace/tracking.php" class="nav-link">Track Package</a></li>
                    <li><a href="/workspace/pricing.php" class="nav-link">Pricing</a></li>
                    <li><a href="/workspace/contact.php" class="nav-link">Contact</a></li>
                    <li><a href="/workspace/quote.php" class="nav-link btn-quote">Get Quote</a></li>
                </ul>
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>
    </header>