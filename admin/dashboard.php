<?php
require_once '../config/database.php';

// Simple authentication (for demo purposes)
session_start();
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle login
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if($_POST['username'] === 'admin' && $_POST['password'] === 'dieng2024') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: dashboard.php');
    exit;
}

if(!$is_logged_in):
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DiengBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="card-body p-5">
                <h3 class="text-center mb-4"><i class="fas fa-user-shield"></i> Admin Login</h3>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <small class="text-muted">Demo: admin / dieng2024</small>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
<?php else: 
// Get statistics
$conn = getConnection();
$stmt = $conn->query("SELECT COUNT(*) as total FROM bookings");
$total_bookings = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM properties WHERE status = 'active'");
$total_properties = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT SUM(total_price) as total_revenue FROM bookings WHERE status = 'confirmed'");
$total_revenue = $stmt->fetch()['total_revenue'] ?? 0;

$stmt = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10");
$recent_bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DiengBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 25px;
        }
        .stat-card {
            border-radius: 15px;
            border: none;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4"><i class="fas fa-mountain"></i> DiengBooking</h4>
                    <a href="#"><i class="fas fa-dashboard"></i> Dashboard</a>
                    <a href="#"><i class="fas fa-building"></i> Properties</a>
                    <a href="#"><i class="fas fa-calendar"></i> Bookings</a>
                    <a href="?logout=1" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard Admin</h2>
                    <div>Welcome, Admin!</div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Booking</h5>
                                <h2><?php echo $total_bookings; ?></h2>
                                <small>Semua waktu</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5>Total Properti</h5>
                                <h2><?php echo $total_properties; ?></h2>
                                <small>Properti aktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5>Total Pendapatan</h5>
                                <h2>Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></h2>
                                <small>Booking confirmed</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Bookings -->
                <div class="card">
                    <div class="card-header">
                        <h5>Booking Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Booking</th>
                                        <th>Customer</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['booking_code']; ?></td>
                                        <td><?php echo $booking['customer_name']; ?></td>
                                        <td><?php echo $booking['check_in']; ?></td>
                                        <td><?php echo $booking['check_out']; ?></td>
                                        <td>Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
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
</body>
</html>
<?php endif; ?>