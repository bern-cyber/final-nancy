<?php
// manage_stocks.php
require_once 'classes/database.php'; // Database connection
require_once 'classes/User.php';      // User class
require_once 'classes/Dashboard.php'; // Dashboard class

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

// Create a Dashboard object (role is no longer needed)
$dashboard = new Dashboard();

// Fetch stock data from the database
$query = "SELECT * FROM stocks";
$stmt = $conn->prepare($query);
$stmt->execute();
$stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Monitor - Food Business System</title>
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
            margin-bottom: 10px; /* Spacing between links */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
            font-weight: bold; /* Bold text */
            transition: background-color 0.3s ease; /* Transition for hover */
        }

        .sidebar a:hover {
            background-color: #b3752b; /* Hover effect */
            color: white; /* Change text color on hover */
        }

        .content {
            margin-left: 250px; /* Make room for the sidebar */
            padding: 20px; /* Content padding */
        }

        .card {
            border-radius: 10px; /* Rounded corners */
            transition: transform 0.3s, box-shadow 0.3s; /* Card hover effects */
        }

        .card:hover {
            transform: translateY(-5px); /* Hover effect */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Shadow on hover */
        }

        .card-header {
            background-color: #d4c09a; /* Header color */
            color: #4E3B31; /* Header text color */
            font-weight: bold; /* Bold header text */
            font-size: 20px; /* Font size for header */
            border-top-left-radius: 10px; /* Rounded corners */
            border-top-right-radius: 10px; /* Rounded corners */
        }

        .icon-style {
            font-size: 25px; /* Icon size */
            margin-right: 10px; /* Spacing */
        }

        .table {
            margin-top: 20px; /* Spacing above the table */
        }

        .btn-custom {
            background-color: #4E3B31; /* Button background color */
            color: white; /* Button text color */
            border-radius: 5px; /* Rounded corners */
            margin-bottom: 15px; /* Spacing below button */
        }

        .btn-custom:hover {
            background-color: #b3752b; /* Hover effect */
            color: white; /* Change text color on hover */
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="css/chicken.png" alt="Logo" class="logo"> <!-- Logo -->
        <h3 class="business-name">Nancy's Foodcart</h3> <!-- Business name -->
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_stocks.php" class="active">Stocks Management</a>
    <a href="sales_track.php">Sales Management</a>
    <a href="expense_track.php">Expense Management</a>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
</div>

<div class="content">
    <h2 class="text-center mt-4" style="color: #4E3B31; font-weight: bold; font-size: 28px;">Stock Monitor</h2>

    <!-- Display success message if stock added -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success" role="alert">
            Stock successfully added!
        </div>
    <?php endif; ?>

    <div class="row mt-4">
        <!-- Add New Stock Button -->
        <div class="col-md-12 mb-4">
            <a href="add_stock.php" class="btn btn-custom mb-3"><i class="fas fa-plus-circle"></i> Add New Stock</a>
        </div>

        <!-- Stock Levels Table -->
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header"><i class="fas fa-boxes icon-style"></i>Current Stock Levels</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($stockData) > 0): ?>
                                <?php foreach ($stockData as $stock): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stock['item']); ?></td>
                                        <td><?php echo htmlspecialchars($stock['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($stock['unit_price']); ?></td>
                                        <td><?php echo htmlspecialchars($stock['total_value']); ?></td>
                                        <td><?php echo htmlspecialchars($stock['last_updated']); ?></td>
                                        <td>
                                            <a href="edit_stock.php?id=<?php echo urlencode($stock['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="delete_stock.php?id=<?php echo urlencode($stock['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No stock available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and FontAwesome -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
