<?php
$page_title = "Track Package";
include 'includes/header.php';
require_once 'config/database.php';

$tracking_result = null;
$tracking_events = [];
$error_message = '';

if (isset($_GET['tracking_number']) && !empty($_GET['tracking_number'])) {
    $tracking_number = trim($_GET['tracking_number']);
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Get shipment details
        $query = "SELECT * FROM shipments WHERE tracking_number = :tracking_number";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':tracking_number', $tracking_number);
        $stmt->execute();
        
        $tracking_result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tracking_result) {
            // Get tracking events
            $events_query = "SELECT * FROM tracking_events WHERE shipment_id = :shipment_id ORDER BY event_time DESC";
            $events_stmt = $db->prepare($events_query);
            $events_stmt->bindParam(':shipment_id', $tracking_result['id']);
            $events_stmt->execute();
            $tracking_events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error_message = "Tracking number not found. Please check and try again.";
        }
        
    } catch(PDOException $exception) {
        $error_message = "Database error: " . $exception->getMessage();
    }
}
?>

<main>
    <section class="tracking-page">
        <div class="container">
            <div class="page-header">
                <h1>Track Your Package</h1>
                <p>Enter your tracking number to get real-time updates on your shipment.</p>
            </div>
            
            <!-- Tracking Search Form -->
            <div class="tracking-search">
                <form class="tracking-form" method="GET">
                    <div class="form-group">
                        <label for="tracking_number">Tracking Number</label>
                        <div class="input-group">
                            <input type="text" 
                                   id="tracking_number" 
                                   name="tracking_number" 
                                   value="<?php echo isset($_GET['tracking_number']) ? htmlspecialchars($_GET['tracking_number']) : ''; ?>"
                                   placeholder="Enter tracking number (e.g., SD001234567890)" 
                                   required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Track Package
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($tracking_result): ?>
                <!-- Tracking Results -->
                <div class="tracking-results">
                    <div class="shipment-header">
                        <h2>Shipment Details</h2>
                        <div class="tracking-number">
                            <strong>Tracking #: <?php echo htmlspecialchars($tracking_result['tracking_number']); ?></strong>
                        </div>
                    </div>
                    
                    <div class="shipment-info">
                        <div class="shipment-grid">
                            <div class="shipment-card">
                                <h3><i class="fas fa-box"></i> Package Information</h3>
                                <div class="info-row">
                                    <span class="label">Service Type:</span>
                                    <span class="value"><?php echo ucfirst(str_replace('_', ' ', $tracking_result['service_type'])); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Weight:</span>
                                    <span class="value"><?php echo $tracking_result['package_weight']; ?> lbs</span>
                                </div>
                                <div class="info-row">
                                    <span class="label">Status:</span>
                                    <span class="value status-<?php echo $tracking_result['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $tracking_result['status'])); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="shipment-card">
                                <h3><i class="fas fa-map-marker-alt"></i> Pickup Address</h3>
                                <div class="address">
                                    <?php echo htmlspecialchars($tracking_result['pickup_address']); ?><br>
                                    <?php echo htmlspecialchars($tracking_result['pickup_city']); ?>, 
                                    <?php echo htmlspecialchars($tracking_result['pickup_state']); ?> 
                                    <?php echo htmlspecialchars($tracking_result['pickup_zip']); ?>
                                </div>
                            </div>
                            
                            <div class="shipment-card">
                                <h3><i class="fas fa-flag-checkered"></i> Delivery Address</h3>
                                <div class="address">
                                    <?php echo htmlspecialchars($tracking_result['delivery_address']); ?><br>
                                    <?php echo htmlspecialchars($tracking_result['delivery_city']); ?>, 
                                    <?php echo htmlspecialchars($tracking_result['delivery_state']); ?> 
                                    <?php echo htmlspecialchars($tracking_result['delivery_zip']); ?>
                                </div>
                                <div class="info-row">
                                    <span class="label">Recipient:</span>
                                    <span class="value"><?php echo htmlspecialchars($tracking_result['receiver_name']); ?></span>
                                </div>
                            </div>
                            
                            <div class="shipment-card">
                                <h3><i class="fas fa-calendar"></i> Delivery Information</h3>
                                <div class="info-row">
                                    <span class="label">Estimated Delivery:</span>
                                    <span class="value">
                                        <?php echo $tracking_result['estimated_delivery'] ? date('M j, Y', strtotime($tracking_result['estimated_delivery'])) : 'TBD'; ?>
                                    </span>
                                </div>
                                <?php if ($tracking_result['actual_delivery']): ?>
                                <div class="info-row">
                                    <span class="label">Delivered On:</span>
                                    <span class="value">
                                        <?php echo date('M j, Y g:i A', strtotime($tracking_result['actual_delivery'])); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tracking Timeline -->
                    <div class="tracking-timeline">
                        <h3>Tracking History</h3>
                        <div class="timeline">
                            <?php foreach ($tracking_events as $event): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h4><?php echo htmlspecialchars($event['status']); ?></h4>
                                            <span class="timeline-date">
                                                <?php echo date('M j, Y g:i A', strtotime($event['event_time'])); ?>
                                            </span>
                                        </div>
                                        <div class="timeline-details">
                                            <?php if ($event['location']): ?>
                                                <p class="location">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <?php echo htmlspecialchars($event['location']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if ($event['description']): ?>
                                                <p class="description">
                                                    <?php echo htmlspecialchars($event['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($_GET['tracking_number']) && empty($error_message)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No results found</h3>
                    <p>Please check your tracking number and try again.</p>
                </div>
            <?php else: ?>
                <!-- Sample Tracking Numbers -->
                <div class="sample-tracking">
                    <h3>Sample Tracking Numbers for Testing</h3>
                    <div class="sample-numbers">
                        <div class="sample-item">
                            <code>SD001234567890</code>
                            <span>Express delivery - In transit</span>
                        </div>
                        <div class="sample-item">
                            <code>SD001234567891</code>
                            <span>Standard shipping - Picked up</span>
                        </div>
                        <div class="sample-item">
                            <code>SD001234567892</code>
                            <span>Overnight delivery - Delivered</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>