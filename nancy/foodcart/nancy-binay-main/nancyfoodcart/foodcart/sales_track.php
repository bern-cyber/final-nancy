<?php
require_once 'classes/database.php';
require_once 'classes/SessionManager.php';
require_once 'Classes/Sales.php';

// Initialize session and check if the user is logged in
$sessionManager = new SessionManager();
if (!$sessionManager->isLoggedIn()) {
    $sessionManager->redirect('login.php');
}

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

// Create Sales object
$sales = new Sales($conn);

// Fetch sales data
$salesData = $sales->getSalesRecords(); // A method to fetch all sales

// Check if the form is submitted for adding a sales record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $amount = $_POST['amount'];

    // Add the sales record
    $sales->addSalesRecord($date, $amount);

    // Refresh the sales data after adding a new record
    $salesData = $sales->getSalesRecords();
}

// Fetch daily sales totals
$dailySalesData = $sales->getDailySales();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Track - Food Business System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Poppins Font -->
    <style>
        /* Custom styles */
        body {
            background: linear-gradient(to right, #f5e0b5, #f3d4a0); /* Gradient background */
            font-family: 'Poppins', sans-serif; /* Use Poppins font */
        }

        .sidebar {
            background-color: #c7a462; /* Sidebar color */
            height: 100vh; /* Full height */
            position: fixed; /* Fixed sidebar */
            width: 250px; /* Width of the sidebar */
            padding-top: 20px; 
        }

        .sidebar a {
            color: #4E3B31; /* Text color */
            padding: 15px; /* Padding for links */
            text-decoration: none; /* Remove underline */
            display: block; /* Block display for links */
            font-weight: bold; /* Bold text */
            transition: background-color 0.3s; /* Smooth transition */
        }

        .sidebar a:hover {
            background-color: #b3752b; /* Hover effect */
            color: white; /* Change text color on hover */
        }

        .content {
            margin-left: 250px; /* Make room for the sidebar */
            padding: 20px;
        }

        .table-container {
            margin-top: 20px;
        }

        .form-container {
            margin-bottom: 30px;
        }

        /* Card and form styles */
        .card {
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .card-header {
            background-color: #d4c09a; /* Card header color */
            font-size: 20px; /* Font size for header */
            font-weight: bold; /* Bold header text */
        }

        .btn-primary {
            background-color: #4E3B31; /* Dark brown background for buttons */
            border: none; /* Remove border */
            color: white; /* White text color */
            transition: background-color 0.3s; /* Smooth transition */
        }

        .btn-primary:hover {
            background-color: #b3752b; /* Darker brown on hover */
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo-container"> <!-- Flex container for logo and business name -->
        <img src="css/chicken.png" alt="Logo" class="logo"> <!-- Logo -->
        <h3 class="business-name">Nancy's Foodcart</h3> <!-- Business name -->
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_stocks.php" class="active">Stocks Management</a>
    <a href="sales_track.php">Sales Management</a>
    <a href="expense_track.php">Expense Management</a>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="text-center" style="color: #4E3B31; font-weight: bold;">Sales Tracking</h2>

    <!-- Form to Add New Sales Record -->
    <div class="form-container card shadow">
        <div class="card-header">Add New Sales Record</div>
        <div class="card-body">
            <form action="sales_track.php" method="post">
                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="date" name="date" required>
                    <label for="date">Date</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Sales Amount" required>
                    <label for="amount">Sales Amount</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Sales</button>
            </form>
        </div>
    </div>

    <!-- Sales Records Table -->
    <div class="table-container card shadow">
        <div class="card-header">Daily Sales Records</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sales Amount (PHP)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesData as $sale): ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['sale_date']); ?></td> <!-- Change 'date' to 'sale_date' -->
                        <td><?= number_format($sale['amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Sales Totals Table -->
    <div class="table-container card shadow">
        <div class="card-header">Daily Sales Totals</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Sales (PHP)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailySalesData as $dailySale): ?>
                    <tr>
                        <td><?= htmlspecialchars($dailySale['sale_date']); ?></td> <!-- Change 'date' to 'sale_date' -->
                        <td><?= number_format($dailySale['total_sales'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
