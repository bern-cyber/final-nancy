<?php
require_once 'classes/database.php';
require_once 'classes/SessionManager.php';
require_once 'classes/Expense.php'; // Adjust the path to point to the correct location

// Initialize session and check if the user is logged in
$sessionManager = new SessionManager();
if (!$sessionManager->isLoggedIn()) {
    $sessionManager->redirect('login.php');
}

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

// Create Expense object
$expense = new Expense($conn);

// Fetch expense data
$expensesData = $expense->getExpenseRecords(); // A method to fetch all expenses

// Check if the form is submitted for adding an expense record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_expense'])) {
        $date = $_POST['date'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];

        // Add the expense record
        $expense->addExpenseRecord($date, $amount, $description);

        // Refresh the expenses data after adding a new record
        $expensesData = $expense->getExpenseRecords();
    } elseif (isset($_POST['filter_date'])) {
        $selectedDate = $_POST['filter_date'];
        // Fetch expenses for the selected date
        $expensesData = $expense->getExpensesByDate($selectedDate);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expense Track - Food Business System</title>
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
            background-color: #c7a462; /* Light brown color for the sidebar */
            height: 100vh; /* Full height */
            position: fixed; /* Fixed sidebar */
            width: 250px; /* Width of the sidebar */
            padding-top: 20px; 
        }

        .sidebar a {
            color: #4E3B31; /* Dark brown color for sidebar text */
            padding: 15px;
            text-decoration: none;
            display: block;
            font-weight: bold; /* Bold text for sidebar links */
            transition: background-color 0.3s; /* Smooth transition */
        }

        .sidebar a:hover {
            background-color: #b3752b; /* Darker brown on hover */
            color: white; /* Change text color to white on hover */
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

        .card {
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .card-header {
            background-color: #d4c09a; /* Header color for cards */
            font-size: 20px; /* Font size for header */
            font-weight: bold; /* Bold header text */
        }

        .btn-primary, .btn-secondary {
            background-color: #4E3B31; /* Dark brown background for buttons */
            border: none; /* Remove border */
            color: white; /* White text color */
            transition: background-color 0.3s; /* Smooth transition */
        }

        .btn-primary:hover, .btn-secondary:hover {
            background-color: #b3752b; /* Darker brown on hover */
        }

        .total-expenses {
            margin-top: 20px; /* Space above total expenses */
            font-weight: bold; /* Bold text */
            font-size: 18px; /* Increased font size */
        }

        h2 {
            color: #4E3B31; /* Consistent header color */
            font-weight: bold; /* Consistent font weight */
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
    <h2 class="text-center">Expense Tracking</h2>

    <!-- Form to Add New Expense Record -->
    <div class="form-container card shadow">
        <div class="card-header">Add New Expense Record</div>
        <div class="card-body">
            <form action="expense_track.php" method="post">
                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="date" name="date" required>
                    <label for="date">Date</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Expense Amount" required>
                    <label for="amount">Expense Amount</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                    <label for="description">Description</label>
                </div>
                <button type="submit" name="add_expense" class="btn btn-primary w-100">Add Expense</button>
            </form>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="form-container card shadow">
        <div class="card-header">Show Expenses by Date</div>
        <div class="card-body">
            <form action="expense_track.php" method="post">
                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="filter_date" name="filter_date" required>
                    <label for="filter_date">Select Date</label>
                </div>
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </form>
        </div>
    </div>

    <!-- Expense Records Table -->
    <div class="table-container card shadow">
        <div class="card-header">Daily Expense Records</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Expense Amount (PHP)</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($expensesData as $expense): ?>
                    <tr>
                        <td><?= htmlspecialchars($expense['date']); ?></td>
                        <td><?= htmlspecialchars($expense['amount']); ?></td>
                        <td><?= htmlspecialchars($expense['description']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-expenses">
                <h5>Total Expenses: 
                    <?= htmlspecialchars(array_sum(array_column($expensesData, 'amount'))); ?> PHP
                </h5>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
