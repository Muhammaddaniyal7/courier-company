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

// Handle quote status updates
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $query = "UPDATE quotes SET status = ?, estimated_cost = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([$_POST['status'], $_POST['estimated_cost'], $_POST['notes'], $_POST['quote_id']]);
    
    if ($result) {
        $message = 'Quote updated successfully';
        $message_type = 'success';
    } else {
        $message = 'Error updating quote';
        $message_type = 'danger';
    }
}

// Get quotes with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where_clause = '';
$params = [];

if (!empty($status_filter)) {
    $where_clause = "WHERE status = ?";
    $params[] = $status_filter;
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM quotes $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_quotes = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_quotes / $limit);

// Get quotes
$query = "SELECT * FROM quotes $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftDelivery - Quote Management</title>
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
            <li class="active">
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
                    <h1 class="h3">Quote Management</h1>
                    <p class="text-muted">Manage customer quote requests and pricing</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Status Filter</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="quoted" <?php echo $status_filter === 'quoted' ? 'selected' : ''; ?>>Quoted</option>
                                <option value="accepted" <?php echo $status_filter === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                <option value="declined" <?php echo $status_filter === 'declined' ? 'selected' : ''; ?>>Declined</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="quotes.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quotes Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quote Requests (<?php echo number_format($total_quotes); ?> total)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Package Details</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Estimated Cost</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quotes as $quote): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($quote['name']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($quote['email']); ?></small>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($quote['phone']); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted">From:</small> <?php echo htmlspecialchars(substr($quote['pickup_address'], 0, 30) . '...'); ?>
                                        <br><small class="text-muted">To:</small> <?php echo htmlspecialchars(substr($quote['delivery_address'], 0, 30) . '...'); ?>
                                    </td>
                                    <td>
                                        <strong><?php echo number_format($quote['package_weight'], 2); ?> lbs</strong>
                                        <?php if ($quote['package_dimensions']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($quote['package_dimensions']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $quote['service_type'])); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_colors = [
                                            'pending' => 'warning',
                                            'quoted' => 'info',
                                            'accepted' => 'success',
                                            'declined' => 'danger'
                                        ];
                                        $color = $status_colors[$quote['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $color; ?>"><?php echo ucfirst($quote['status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($quote['estimated_cost']): ?>
                                            $<?php echo number_format($quote['estimated_cost'], 2); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not quoted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($quote['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="updateQuote(<?php echo $quote['id']; ?>, '<?php echo $quote['status']; ?>', '<?php echo $quote['estimated_cost']; ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Quotes pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Quote Modal -->
    <div class="modal fade" id="updateQuoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Quote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="quote_id" id="quote_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="quote_status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="quoted">Quoted</option>
                                <option value="accepted">Accepted</option>
                                <option value="declined">Declined</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimated Cost ($)</label>
                            <input type="number" step="0.01" name="estimated_cost" id="estimated_cost" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this quote..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Quote</button>
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

        // Update quote function
        function updateQuote(quoteId, currentStatus, currentCost) {
            document.getElementById('quote_id').value = quoteId;
            document.getElementById('quote_status').value = currentStatus;
            document.getElementById('estimated_cost').value = currentCost || '';
            
            const updateModal = new bootstrap.Modal(document.getElementById('updateQuoteModal'));
            updateModal.show();
        }
    </script>
</body>
</html>