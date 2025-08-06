<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$message = '';
$message_type = '';

// Handle adding new tracking event
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_event') {
    $query = "INSERT INTO tracking_events (shipment_id, status, location, description) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([$_POST['shipment_id'], $_POST['status'], $_POST['location'], $_POST['description']]);
    
    if ($result) {
        // Update shipment status if provided
        if (!empty($_POST['update_shipment_status'])) {
            $update_query = "UPDATE shipments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$_POST['update_shipment_status'], $_POST['shipment_id']]);
        }
        
        $message = 'Tracking event added successfully';
        $message_type = 'success';
    } else {
        $message = 'Error adding tracking event';
        $message_type = 'danger';
    }
}

// Get shipment details if ID is provided
$shipment = null;
$tracking_events = [];
if (isset($_GET['id'])) {
    $shipment_id = (int)$_GET['id'];
    
    // Get shipment details
    $query = "SELECT s.*, c.first_name, c.last_name, c.email as sender_email 
              FROM shipments s 
              LEFT JOIN customers c ON s.sender_id = c.id 
              WHERE s.id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$shipment_id]);
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($shipment) {
        // Get tracking events
        $query = "SELECT * FROM tracking_events WHERE shipment_id = ? ORDER BY event_time DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$shipment_id]);
        $tracking_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Get all shipments for dropdown
$shipments_query = "SELECT id, tracking_number, receiver_name FROM shipments ORDER BY created_at DESC LIMIT 100";
$shipments_stmt = $db->prepare($shipments_query);
$shipments_stmt->execute();
$all_shipments = $shipments_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftDelivery - Tracking Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-shipping-fast"></i> SwiftDelivery</h4>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li>
                <a href="shipments.php"><i class="fas fa-box"></i> Shipments</a>
            </li>
            <li>
                <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
            </li>
            <li>
                <a href="quotes.php"><i class="fas fa-file-invoice-dollar"></i> Quotes</a>
            </li>
            <li class="active">
                <a href="tracking.php"><i class="fas fa-route"></i> Tracking</a>
            </li>
            <li>
                <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            </li>
            <li>
                <a href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-primary">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="ms-auto">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h1 class="h3">Tracking Management</h1>
                    <p class="text-muted">View and manage shipment tracking information</p>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="fas fa-plus"></i> Add Tracking Event
                    </button>
                </div>
            </div>

            <?php if (!$shipment): ?>
            <!-- Shipment Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Select Shipment to Track</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Choose Shipment</label>
                            <select class="form-select" onchange="if(this.value) window.location.href='tracking.php?id='+this.value">
                                <option value="">Select a shipment...</option>
                                <?php foreach ($all_shipments as $ship): ?>
                                <option value="<?php echo $ship['id']; ?>">
                                    <?php echo htmlspecialchars($ship['tracking_number'] . ' - ' . $ship['receiver_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($shipment): ?>
            <!-- Shipment Details -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Shipment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Basic Information</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Tracking Number:</strong></td>
                                            <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Service Type:</strong></td>
                                            <td><span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $shipment['service_type'])); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Current Status:</strong></td>
                                            <td>
                                                <?php
                                                $status_colors = [
                                                    'pending' => 'warning',
                                                    'picked_up' => 'info',
                                                    'in_transit' => 'primary',
                                                    'out_for_delivery' => 'success',
                                                    'delivered' => 'success',
                                                    'failed_delivery' => 'danger'
                                                ];
                                                $color = $status_colors[$shipment['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $color; ?>"><?php echo ucfirst(str_replace('_', ' ', $shipment['status'])); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Package Weight:</strong></td>
                                            <td><?php echo number_format($shipment['package_weight'], 2); ?> lbs</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Cost:</strong></td>
                                            <td>$<?php echo number_format($shipment['total_cost'], 2); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Sender & Receiver</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Sender:</strong></td>
                                            <td>
                                                <?php if ($shipment['first_name']): ?>
                                                    <?php echo htmlspecialchars($shipment['first_name'] . ' ' . $shipment['last_name']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Receiver:</strong></td>
                                            <td><?php echo htmlspecialchars($shipment['receiver_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Receiver Phone:</strong></td>
                                            <td><?php echo htmlspecialchars($shipment['receiver_phone']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>From:</strong></td>
                                            <td><?php echo htmlspecialchars($shipment['pickup_city'] . ', ' . $shipment['pickup_state']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>To:</strong></td>
                                            <td><?php echo htmlspecialchars($shipment['delivery_city'] . ', ' . $shipment['delivery_state']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="addTrackingEvent(<?php echo $shipment['id']; ?>)">
                                    <i class="fas fa-plus"></i> Add Event
                                </button>
                                <a href="shipments.php?edit=<?php echo $shipment['id']; ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit Shipment
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tracking Timeline</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($tracking_events)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-route fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tracking events yet</h5>
                            <p class="text-muted">Add the first tracking event to start monitoring this shipment.</p>
                            <button type="button" class="btn btn-primary" onclick="addTrackingEvent(<?php echo $shipment['id']; ?>)">
                                <i class="fas fa-plus"></i> Add First Event
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($tracking_events as $index => $event): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <?php if ($index === 0): ?>
                                        <i class="fas fa-map-marker-alt text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle text-primary"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($event['status']); ?></h6>
                                            <?php if ($event['location']): ?>
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($event['location']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if ($event['description']): ?>
                                                <p class="mb-0"><?php echo htmlspecialchars($event['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($event['event_time'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Tracking Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Tracking Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add_event">
                    <input type="hidden" name="shipment_id" id="event_shipment_id" value="<?php echo $shipment['id'] ?? ''; ?>">
                    <div class="modal-body">
                        <?php if (!$shipment): ?>
                        <div class="mb-3">
                            <label class="form-label">Select Shipment</label>
                            <select name="shipment_id" class="form-select" required>
                                <option value="">Choose shipment...</option>
                                <?php foreach ($all_shipments as $ship): ?>
                                <option value="<?php echo $ship['id']; ?>">
                                    <?php echo htmlspecialchars($ship['tracking_number'] . ' - ' . $ship['receiver_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Event Status</label>
                            <input type="text" name="status" class="form-control" required placeholder="e.g., Package Picked Up, In Transit, Delivered">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Location (Optional)</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g., New York, NY">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="Describe what happened..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Update Shipment Status (Optional)</label>
                            <select name="update_shipment_status" class="form-select">
                                <option value="">Don't update shipment status</option>
                                <option value="pending">Pending</option>
                                <option value="picked_up">Picked Up</option>
                                <option value="in_transit">In Transit</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="failed_delivery">Failed Delivery</option>
                            </select>
                            <small class="form-text text-muted">This will also update the main shipment status</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        // Add tracking event function
        function addTrackingEvent(shipmentId) {
            if (shipmentId) {
                document.getElementById('event_shipment_id').value = shipmentId;
            }
            const eventModal = new bootstrap.Modal(document.getElementById('addEventModal'));
            eventModal.show();
        }
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 0;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
        }

        .timeline-content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 3px solid #2563eb;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }
    </style>
</body>
</html>