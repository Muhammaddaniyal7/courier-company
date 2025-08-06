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

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Create new shipment
                $tracking_number = 'SD' . date('YmdHis');
                $query = "INSERT INTO shipments (tracking_number, sender_id, receiver_name, receiver_phone, receiver_email, 
                         pickup_address, pickup_city, pickup_state, pickup_zip, delivery_address, delivery_city, 
                         delivery_state, delivery_zip, package_weight, package_dimensions, service_type, total_cost, notes) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($query);
                $result = $stmt->execute([
                    $tracking_number, $_POST['sender_id'], $_POST['receiver_name'], $_POST['receiver_phone'],
                    $_POST['receiver_email'], $_POST['pickup_address'], $_POST['pickup_city'], $_POST['pickup_state'],
                    $_POST['pickup_zip'], $_POST['delivery_address'], $_POST['delivery_city'], $_POST['delivery_state'],
                    $_POST['delivery_zip'], $_POST['package_weight'], $_POST['package_dimensions'], 
                    $_POST['service_type'], $_POST['total_cost'], $_POST['notes']
                ]);
                
                if ($result) {
                    $shipment_id = $db->lastInsertId();
                    // Add initial tracking event
                    $tracking_query = "INSERT INTO tracking_events (shipment_id, status, description) VALUES (?, 'Package Created', 'Shipment created in system')";
                    $tracking_stmt = $db->prepare($tracking_query);
                    $tracking_stmt->execute([$shipment_id]);
                    
                    $message = 'Shipment created successfully with tracking number: ' . $tracking_number;
                    $message_type = 'success';
                } else {
                    $message = 'Error creating shipment';
                    $message_type = 'danger';
                }
                break;
                
            case 'update_status':
                $query = "UPDATE shipments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $stmt = $db->prepare($query);
                $result = $stmt->execute([$_POST['status'], $_POST['shipment_id']]);
                
                if ($result) {
                    // Add tracking event
                    $tracking_query = "INSERT INTO tracking_events (shipment_id, status, description) VALUES (?, ?, ?)";
                    $tracking_stmt = $db->prepare($tracking_query);
                    $tracking_stmt->execute([$_POST['shipment_id'], ucfirst(str_replace('_', ' ', $_POST['status'])), $_POST['notes'] ?? 'Status updated']);
                    
                    $message = 'Shipment status updated successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating shipment status';
                    $message_type = 'danger';
                }
                break;
        }
    }
}

// Get shipments with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(s.tracking_number LIKE ? OR s.receiver_name LIKE ? OR CONCAT(c.first_name, ' ', c.last_name) LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "s.status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM shipments s LEFT JOIN customers c ON s.sender_id = c.id $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_shipments = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_shipments / $limit);

// Get shipments
$query = "SELECT s.*, c.first_name, c.last_name, c.email as sender_email FROM shipments s 
          LEFT JOIN customers c ON s.sender_id = c.id 
          $where_clause 
          ORDER BY s.created_at DESC 
          LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get customers for dropdown
$customers_query = "SELECT id, CONCAT(first_name, ' ', last_name) as name FROM customers ORDER BY first_name";
$customers_stmt = $db->prepare($customers_query);
$customers_stmt->execute();
$customers = $customers_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftDelivery - Shipment Management</title>
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
            <li class="active">
                <a href="shipments.php"><i class="fas fa-box"></i> Shipments</a>
            </li>
            <li>
                <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
            </li>
            <li>
                <a href="quotes.php"><i class="fas fa-file-invoice-dollar"></i> Quotes</a>
            </li>
            <li>
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
                    <h1 class="h3">Shipment Management</h1>
                    <p class="text-muted">Manage all shipments and tracking information</p>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShipmentModal">
                        <i class="fas fa-plus"></i> Add New Shipment
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by tracking number, receiver, or sender">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="picked_up" <?php echo $status_filter === 'picked_up' ? 'selected' : ''; ?>>Picked Up</option>
                                <option value="in_transit" <?php echo $status_filter === 'in_transit' ? 'selected' : ''; ?>>In Transit</option>
                                <option value="out_for_delivery" <?php echo $status_filter === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="failed_delivery" <?php echo $status_filter === 'failed_delivery' ? 'selected' : ''; ?>>Failed Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="shipments.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Shipments Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Shipments (<?php echo number_format($total_shipments); ?> total)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking Number</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Weight</th>
                                    <th>Cost</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td>
                                        <strong class="text-primary"><?php echo htmlspecialchars($shipment['tracking_number']); ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($shipment['first_name']): ?>
                                            <?php echo htmlspecialchars($shipment['first_name'] . ' ' . $shipment['last_name']); ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($shipment['sender_email']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($shipment['receiver_name']); ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($shipment['receiver_phone']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $shipment['service_type'])); ?></span>
                                    </td>
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
                                    <td><?php echo number_format($shipment['package_weight'], 2); ?> lbs</td>
                                    <td>$<?php echo number_format($shipment['total_cost'], 2); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($shipment['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="updateStatus(<?php echo $shipment['id']; ?>, '<?php echo $shipment['status']; ?>', '<?php echo htmlspecialchars($shipment['tracking_number']); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="tracking.php?id=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-route"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Shipments pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shipment Modal -->
    <div class="modal fade" id="addShipmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Shipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Sender Information</h6>
                                <div class="mb-3">
                                    <label class="form-label">Sender</label>
                                    <select name="sender_id" class="form-select">
                                        <option value="">Select Customer</option>
                                        <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pickup Address</label>
                                    <textarea name="pickup_address" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="pickup_city" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="pickup_state" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">ZIP</label>
                                        <input type="text" name="pickup_zip" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Receiver Information</h6>
                                <div class="mb-3">
                                    <label class="form-label">Receiver Name</label>
                                    <input type="text" name="receiver_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="receiver_phone" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="receiver_email" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Delivery Address</label>
                                    <textarea name="delivery_address" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="delivery_city" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="delivery_state" class="form-control" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">ZIP</label>
                                        <input type="text" name="delivery_zip" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Weight (lbs)</label>
                                    <input type="number" step="0.01" name="package_weight" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Dimensions</label>
                                    <input type="text" name="package_dimensions" class="form-control" placeholder="L x W x H">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Service Type</label>
                                    <select name="service_type" class="form-select" required>
                                        <option value="standard">Standard</option>
                                        <option value="express">Express</option>
                                        <option value="overnight">Overnight</option>
                                        <option value="same_day">Same Day</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Cost ($)</label>
                                    <input type="number" step="0.01" name="total_cost" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Shipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Shipment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="shipment_id" id="status_shipment_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" id="status_tracking_number" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Status</label>
                            <select name="status" id="status_select" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="picked_up">Picked Up</option>
                                <option value="in_transit">In Transit</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="failed_delivery">Failed Delivery</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Add status update notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
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

        // Update status function
        function updateStatus(shipmentId, currentStatus, trackingNumber) {
            document.getElementById('status_shipment_id').value = shipmentId;
            document.getElementById('status_tracking_number').value = trackingNumber;
            document.getElementById('status_select').value = currentStatus;
            
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        }
    </script>
</body>
</html>