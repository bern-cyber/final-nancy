<?php
// dashboard.php
require_once 'classes/database.php';
require_once 'classes/User.php';
require_once 'classes/Dashboard.php';

// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

// Create the User object
$user = new User($conn);
$dashboard = new Dashboard(); // No role is passed to the dashboard

// Fetch stock data from the database
$query = "SELECT item, quantity FROM stocks"; // Changed item_name to item
$stmt = $conn->prepare($query);
$stmt->execute();
$stockData = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stockData[$row['item']] = $row['quantity']; // Update here
}

// Fetch sales for today
$query = "SELECT SUM(amount) AS sales_today FROM sales WHERE DATE(sale_date) = CURDATE()"; // Keep as is
$stmt = $conn->prepare($query);
$stmt->execute();
$salesRow = $stmt->fetch(PDO::FETCH_ASSOC);
$salesToday = $salesRow['sales_today'] ?? 0;

// Fetch expenses for today
$query = "SELECT SUM(amount) AS expenses_today FROM expenses WHERE DATE(date) = CURDATE()"; // Updated to match the column name in your database
$stmt = $conn->prepare($query);
$stmt->execute();
$expensesRow = $stmt->fetch(PDO::FETCH_ASSOC);
$expensesToday = $expensesRow['expenses_today'] ?? 0;

// Calculate profit
$profitToday = $salesToday - $expensesToday;

// Get current date and time
$currentDateTime = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Food Business System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"> <!-- Custom Styles for Better UI -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Poppins Font -->
    <style>
        /* Custom styles */
        body {
            background: linear-gradient(to right, #f5e0b5, #f3d4a0); /* Gradient background */
            font-family: 'Poppins', sans-serif; /* Use Poppins font */
        }

        .sidebar {
            background-color: #c7a462; /* Light brown color for the sidebar */
            height: 100vh; /* Full height */
            position: fixed; /* Fixed sidebar */
            width: 250px; /* Width of the sidebar */
            padding-top: 20px; 
            transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }

        .sidebar a {
            color: #4E3B31; /* Dark brown color for sidebar text */
            padding: 15px;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            border-radius: 5px;
            font-size: 16px; /* Slightly larger font size */
            font-weight: bold; /* Bold text for sidebar links */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        .sidebar a:hover {
            background-color: #b3752b; /* Darker brown on hover */
            color: white; /* Change text color to white on hover */
        }

        .content {
            margin-left: 250px; /* Make room for the sidebar */
            padding: 20px;
        }

        .card {
            height: 200px; /* Set a uniform height for cards */
            border-radius: 10px; /* Rounded corners */
            transition: transform 0.3s, box-shadow 0.3s; /* Smooth scaling and shadow transition */
        }

        .card:hover {
            transform: translateY(-5px); /* Slightly lift the card on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Add shadow on hover */
        }

        .card-header {
            background-color: #d4c09a; /* Header color for cards */
            color: #4E3B31; /* Dark brown text color */
            font-weight: bold;
            font-size: 20px; /* Slightly larger font size */
            border-top-left-radius: 10px; /* Rounded corners for header */
            border-top-right-radius: 10px; /* Rounded corners for header */
        }

        .card-body h3 {
            color: #4E3B31; /* Dark brown text color */
            font-size: 24px; /* Larger font size */
            font-weight: bold; /* Bold text */
        }

        .icon-style {
            font-size: 25px;
            margin-right: 10px;
        }

        /* Custom logo style */
        .logo {
            width: 50px; /* Adjust size as needed */
            height: auto;
        }

        /* Custom card colors */
        .sales-card {
            background: #f9e9c7; /* Light yellowish-brown */
            border-left: 5px solid brown; /* Green border */
        }

        .expenses-card {
            background: #f9e9c7; /* Light yellowish-brown */
            border-left: 5px solid brown; /* Orange border */
        }

        .profit-card {
            background: #f9e9c7; /* Light yellowish-brown */
            border-left: 5px solid brown; /* Blue border */
        }

        .stock-card {
            background: #ffebc8; /* Very light brown */
            border-left: 5px solid brown; /* Yellow border */
        }

        /* Date and Time Card Customization */
        .date-time-card {
            width: 350px; /* Set to desired width */
            text-align: center; 
            background: #ffebc8;
            margin: 0 auto; /* Center the card */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            border-left: 5px solid brown; 
        }

        /* Animation for page load */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
<!-- Sidebar -->
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
<div class="content fade-in">
    <h2 class="text-center mt-4" style="color: #4E3B31; font-weight: bold; font-size: 28px;">WELCOME TO DASHBOARD!</h2>

    <div class="row mt-4">
        <!-- Date and Time Card -->
        <div class="col-md-12 mb-4">
            <div class="card shadow date-time-card custom-card">
                <div class="card-header"><i class="fas fa-calendar-alt icon-style"></i>Current Date and Time</div>
                <div class="card-body">
                    <h3><?php echo $currentDateTime; ?></h3>
                </div>
            </div>
        </div>

        <!-- Stock Levels Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow stock-card custom-card">
                <div class="card-header"><i class="fas fa-boxes icon-style"></i>Stock Levels</div>
                <div class="card-body">
                    <?php $dashboard->showStockLevels($stockData); ?>
                </div>
            </div>
        </div>

        <!-- Sales Today Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow sales-card custom-card">
                <div class="card-header"><i class="fas fa-chart-line icon-style"></i>Today's Sales</div>
                <div class="card-body">
                    <h3><?php echo number_format($salesToday, 2); ?> PHP</h3>
                </div>
            </div>
        </div>

        <!-- Expenses Today Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow expenses-card custom-card">
                <div class="card-header"><i class="fas fa-money-bill-wave icon-style"></i>Today's Expenses</div>
                <div class="card-body">
                    <h3><?php echo number_format($expensesToday, 2); ?> PHP</h3>
                </div>
            </div>
        </div>

        <!-- Profit Today Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow profit-card custom-card">
                <div class="card-header"><i class="fas fa-chart-pie icon-style"></i>Today's Profit</div>
                <div class="card-body">
                    <h3><?php echo number_format($profitToday, 2); ?> PHP</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
