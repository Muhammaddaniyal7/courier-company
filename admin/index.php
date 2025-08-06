<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in (simple check for now)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get dashboard statistics
$stats = [];

// Total shipments
$query = "SELECT COUNT(*) as total_shipments FROM shipments";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_shipments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_shipments'];

// Total customers
$query = "SELECT COUNT(*) as total_customers FROM customers";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];

// Pending quotes
$query = "SELECT COUNT(*) as pending_quotes FROM quotes WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['pending_quotes'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending_quotes'];

// Revenue this month
$query = "SELECT SUM(total_cost) as monthly_revenue FROM shipments WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['monthly_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['monthly_revenue'] ?? 0;

// Shipments by status
$query = "SELECT status, COUNT(*) as count FROM shipments GROUP BY status";
$stmt = $db->prepare($query);
$stmt->execute();
$shipment_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent shipments
$query = "SELECT s.*, c.first_name, c.last_name FROM shipments s 
          LEFT JOIN customers c ON s.sender_id = c.id 
          ORDER BY s.created_at DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Service type distribution
$query = "SELECT service_type, COUNT(*) as count FROM shipments GROUP BY service_type";
$stmt = $db->prepare($query);
$stmt->execute();
$service_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftDelivery - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-shipping-fast"></i> SwiftDelivery</h4>
        </div>
        <ul class="list-unstyled components">
            <li class="active">
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
                            <i class="fas fa-user-circle"></i> Admin User
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
            <div class="row">
                <div class="col-12">
                    <h1 class="h3 mb-4">Dashboard Overview</h1>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['total_shipments']); ?></h4>
                                    <p class="mb-0">Total Shipments</p>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['total_customers']); ?></h4>
                                    <p class="mb-0">Total Customers</p>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($stats['pending_quotes']); ?></h4>
                                    <p class="mb-0">Pending Quotes</p>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>$<?php echo number_format($stats['monthly_revenue'], 2); ?></h4>
                                    <p class="mb-0">Monthly Revenue</p>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Shipment Status Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Service Types</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="serviceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Shipments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Shipments</h5>
                            <a href="shipments.php" class="btn btn-primary btn-sm">View All</a>
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
                                            <th>Cost</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_shipments as $shipment): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($shipment['tracking_number']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($shipment['first_name'] . ' ' . $shipment['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($shipment['receiver_name']); ?></td>
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
                                            <td>$<?php echo number_format($shipment['total_cost'], 2); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($shipment['created_at'])); ?></td>
                                            <td>
                                                <a href="shipments.php?edit=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="tracking.php?id=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        // Status Chart
        const statusData = <?php echo json_encode($shipment_status); ?>;
        const statusLabels = statusData.map(item => item.status.replace('_', ' ').toUpperCase());
        const statusCounts = statusData.map(item => item.count);

        const statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Shipments',
                    data: statusCounts,
                    backgroundColor: [
                        '#ffc107', '#17a2b8', '#007bff', '#28a745', '#28a745', '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Service Chart
        const serviceData = <?php echo json_encode($service_distribution); ?>;
        const serviceLabels = serviceData.map(item => item.service_type.replace('_', ' ').toUpperCase());
        const serviceCounts = serviceData.map(item => item.count);

        const serviceChart = new Chart(document.getElementById('serviceChart'), {
            type: 'doughnut',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceCounts,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>