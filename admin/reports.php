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

// Get date range from query params or default to last 30 days
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Revenue data
$revenue_query = "SELECT DATE(created_at) as date, SUM(total_cost) as revenue, COUNT(*) as shipments 
                  FROM shipments 
                  WHERE DATE(created_at) BETWEEN ? AND ? 
                  GROUP BY DATE(created_at) 
                  ORDER BY date";
$revenue_stmt = $db->prepare($revenue_query);
$revenue_stmt->execute([$start_date, $end_date]);
$revenue_data = $revenue_stmt->fetchAll(PDO::FETCH_ASSOC);

// Service type performance
$service_query = "SELECT service_type, COUNT(*) as count, SUM(total_cost) as revenue, 
                  AVG(total_cost) as avg_cost 
                  FROM shipments 
                  WHERE DATE(created_at) BETWEEN ? AND ? 
                  GROUP BY service_type";
$service_stmt = $db->prepare($service_query);
$service_stmt->execute([$start_date, $end_date]);
$service_data = $service_stmt->fetchAll(PDO::FETCH_ASSOC);

// Status distribution
$status_query = "SELECT status, COUNT(*) as count 
                 FROM shipments 
                 WHERE DATE(created_at) BETWEEN ? AND ? 
                 GROUP BY status";
$status_stmt = $db->prepare($status_query);
$status_stmt->execute([$start_date, $end_date]);
$status_data = $status_stmt->fetchAll(PDO::FETCH_ASSOC);

// Top customers
$customers_query = "SELECT c.first_name, c.last_name, c.email, COUNT(s.id) as shipment_count, 
                    SUM(s.total_cost) as total_spent 
                    FROM customers c 
                    INNER JOIN shipments s ON c.id = s.sender_id 
                    WHERE DATE(s.created_at) BETWEEN ? AND ? 
                    GROUP BY c.id 
                    ORDER BY total_spent DESC 
                    LIMIT 10";
$customers_stmt = $db->prepare($customers_query);
$customers_stmt->execute([$start_date, $end_date]);
$top_customers = $customers_stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly trends (last 12 months)
$trends_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                 COUNT(*) as shipments, 
                 SUM(total_cost) as revenue 
                 FROM shipments 
                 WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH) 
                 GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                 ORDER BY month";
$trends_stmt = $db->prepare($trends_query);
$trends_stmt->execute();
$monthly_trends = $trends_stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary statistics
$summary_query = "SELECT 
    COUNT(*) as total_shipments,
    SUM(total_cost) as total_revenue,
    AVG(total_cost) as avg_revenue,
    COUNT(DISTINCT sender_id) as unique_customers,
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_count
    FROM shipments 
    WHERE DATE(created_at) BETWEEN ? AND ?";
$summary_stmt = $db->prepare($summary_query);
$summary_stmt->execute([$start_date, $end_date]);
$summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);

$delivery_rate = $summary['total_shipments'] > 0 ? 
    ($summary['delivered_count'] / $summary['total_shipments']) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftDelivery - Reports & Analytics</title>
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
            <li>
                <a href="tracking.php"><i class="fas fa-route"></i> Tracking</a>
            </li>
            <li class="active">
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
            <div class="row mb-4">
                <div class="col-md-6">
                    <h1 class="h3">Reports & Analytics</h1>
                    <p class="text-muted">Business insights and performance metrics</p>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                        <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>$<?php echo number_format($summary['total_revenue'], 2); ?></h4>
                                    <p class="mb-0">Total Revenue</p>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
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
                                    <h4><?php echo number_format($summary['total_shipments']); ?></h4>
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
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo number_format($summary['unique_customers']); ?></h4>
                                    <p class="mb-0">Active Customers</p>
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
                                    <h4><?php echo number_format($delivery_rate, 1); ?>%</h4>
                                    <p class="mb-0">Delivery Rate</p>
                                </div>
                                <div class="stats-icon">
                                    <i class="fas fa-truck fa-2x"></i>
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
                            <h5 class="card-title mb-0">Revenue Trends (Last 12 Months)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Service Type Performance</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="serviceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Analysis -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Service Performance Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Service Type</th>
                                            <th>Count</th>
                                            <th>Revenue</th>
                                            <th>Avg Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($service_data as $service): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo ucfirst(str_replace('_', ' ', $service['service_type'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($service['count']); ?></td>
                                            <td>$<?php echo number_format($service['revenue'], 2); ?></td>
                                            <td>$<?php echo number_format($service['avg_cost'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Top Customers</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($top_customers)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No customer data</h5>
                                    <p class="text-muted">No shipments in the selected date range.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Shipments</th>
                                                <th>Total Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_customers as $customer): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($customer['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $customer['shipment_count']; ?></span>
                                                </td>
                                                <td>
                                                    <strong>$<?php echo number_format($customer['total_spent'], 2); ?></strong>
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

            <!-- Status Analysis -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Shipment Status Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Quick Metrics</h5>
                            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <h4 class="text-primary">$<?php echo number_format($summary['avg_revenue'], 2); ?></h4>
                                    <p class="text-muted mb-0">Average Revenue per Shipment</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success"><?php echo number_format($summary['delivered_count']); ?></h4>
                                    <p class="text-muted mb-0">Successful Deliveries</p>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2">
                                <a href="shipments.php" class="btn btn-outline-primary">
                                    <i class="fas fa-box"></i> Manage Shipments
                                </a>
                                <a href="customers.php" class="btn btn-outline-success">
                                    <i class="fas fa-users"></i> View Customers
                                </a>
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

        // Revenue Chart
        const monthlyData = <?php echo json_encode($monthly_trends); ?>;
        const revenueLabels = monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        const revenueValues = monthlyData.map(item => parseFloat(item.revenue));
        const shipmentValues = monthlyData.map(item => parseInt(item.shipments));

        const revenueChart = new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenueValues,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Shipments',
                    data: shipmentValues,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Shipments'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Service Chart
        const serviceData = <?php echo json_encode($service_data); ?>;
        const serviceLabels = serviceData.map(item => item.service_type.replace('_', ' ').toUpperCase());
        const serviceCounts = serviceData.map(item => item.count);

        const serviceChart = new Chart(document.getElementById('serviceChart'), {
            type: 'doughnut',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceCounts,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Status Chart
        const statusData = <?php echo json_encode($status_data); ?>;
        const statusLabels = statusData.map(item => item.status.replace('_', ' ').toUpperCase());
        const statusCounts = statusData.map(item => item.count);

        const statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Shipments',
                    data: statusCounts,
                    backgroundColor: ['#ffc107', '#17a2b8', '#007bff', '#28a745', '#28a745', '#dc3545']
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
    </script>
</body>
</html>