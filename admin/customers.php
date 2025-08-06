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
                $query = "INSERT INTO customers (first_name, last_name, email, phone, address, city, state, zip_code, country) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $result = $stmt->execute([
                    $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'],
                    $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip_code'], $_POST['country']
                ]);
                
                if ($result) {
                    $message = 'Customer created successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Error creating customer. Email might already exist.';
                    $message_type = 'danger';
                }
                break;
                
            case 'update':
                $query = "UPDATE customers SET first_name = ?, last_name = ?, email = ?, phone = ?, 
                         address = ?, city = ?, state = ?, zip_code = ?, country = ?, updated_at = CURRENT_TIMESTAMP 
                         WHERE id = ?";
                $stmt = $db->prepare($query);
                $result = $stmt->execute([
                    $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'],
                    $_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip_code'], 
                    $_POST['country'], $_POST['customer_id']
                ]);
                
                if ($result) {
                    $message = 'Customer updated successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating customer';
                    $message_type = 'danger';
                }
                break;
        }
    }
}

// Get customers with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where_clause = '';
$params = [];

if (!empty($search)) {
    $where_clause = "WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR phone LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM customers $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_customers = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_customers / $limit);

// Get customers with shipment count
$query = "SELECT c.*, COUNT(s.id) as shipment_count, MAX(s.created_at) as last_shipment 
          FROM customers c 
          LEFT JOIN shipments s ON c.id = s.sender_id 
          $where_clause 
          GROUP BY c.id 
          ORDER BY c.created_at DESC 
          LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get customer details if viewing
$customer_details = null;
$customer_shipments = [];
if (isset($_GET['view'])) {
    $customer_id = (int)$_GET['view'];
    
    // Get customer details
    $query = "SELECT * FROM customers WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$customer_id]);
    $customer_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($customer_details) {
        // Get customer shipments
        $query = "SELECT * FROM shipments WHERE sender_id = ? ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$customer_id]);
        $customer_shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftDelivery - Customer Management</title>
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
            <li class="active">
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

            <?php if ($customer_details): ?>
            <!-- Customer Details View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3">Customer Details</h1>
                    <p class="text-muted"><?php echo htmlspecialchars($customer_details['first_name'] . ' ' . $customer_details['last_name']); ?></p>
                </div>
                <div>
                    <a href="customers.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="button" class="btn btn-primary" onclick="editCustomer(<?php echo $customer_details['id']; ?>)">
                        <i class="fas fa-edit"></i> Edit Customer
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo htmlspecialchars($customer_details['first_name'] . ' ' . $customer_details['last_name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo htmlspecialchars($customer_details['email']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?php echo htmlspecialchars($customer_details['phone']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($customer_details['address']); ?><br>
                                        <?php echo htmlspecialchars($customer_details['city'] . ', ' . $customer_details['state'] . ' ' . $customer_details['zip_code']); ?><br>
                                        <?php echo htmlspecialchars($customer_details['country']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Joined:</strong></td>
                                    <td><?php echo date('M j, Y', strtotime($customer_details['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Shipment History (<?php echo count($customer_shipments); ?> shipments)</h5>
                            <a href="shipments.php?customer=<?php echo $customer_details['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i> Create Shipment
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($customer_shipments)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No shipments yet</h5>
                                    <p class="text-muted">This customer hasn't made any shipments.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tracking Number</th>
                                                <th>Receiver</th>
                                                <th>Service</th>
                                                <th>Status</th>
                                                <th>Cost</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customer_shipments as $shipment): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($shipment['tracking_number']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($shipment['receiver_name']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $shipment['service_type'])); ?></span></td>
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
                                                <td>$<?php echo number_format($shipment['total_cost'], 2); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($shipment['created_at'])); ?></td>
                                                <td>
                                                    <a href="tracking.php?id=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- Customer List View -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h1 class="h3">Customer Management</h1>
                    <p class="text-muted">Manage customer information and view shipment history</p>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        <i class="fas fa-plus"></i> Add New Customer
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search Customers</label>
                            <input type="text" class="form-control" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by name, email, or phone">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="customers.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customers (<?php echo number_format($total_customers); ?> total)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th>Shipments</th>
                                    <th>Last Activity</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($customer['email']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($customer['phone']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($customer['city'] . ', ' . $customer['state']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($customer['country']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $customer['shipment_count']; ?> shipments</span>
                                    </td>
                                    <td>
                                        <?php if ($customer['last_shipment']): ?>
                                            <?php echo date('M j, Y', strtotime($customer['last_shipment'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">No shipments</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="customers.php?view=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCustomer(<?php echo $customer['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Customers pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" name="zip_code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control" value="USA" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="customer_id" id="edit_customer_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="edit_email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" id="edit_phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" id="edit_city" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" id="edit_state" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" name="zip_code" id="edit_zip_code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" id="edit_country" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
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

        // Edit customer function
        function editCustomer(customerId) {
            // In a real application, you would fetch customer data via AJAX
            // For now, we'll show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
            editModal.show();
        }
    </script>
</body>
</html>