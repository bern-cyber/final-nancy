<?php
// add_stock.php
require_once 'classes/database.php'; // Include the database connection file

// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    // Calculate the total value of the stock
    $total_value = $quantity * $unit_price;

    // Insert the new stock into the database
    $query = "INSERT INTO stocks (item, quantity, unit_price, total_value, last_updated) 
              VALUES (:item, :quantity, :unit_price, :total_value, NOW())";
    
    $stmt = $conn->prepare($query);
    
    $stmt->bindParam(':item', $item);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':unit_price', $unit_price);
    $stmt->bindParam(':total_value', $total_value);

    // Execute the query and check if the insertion was successful
    if ($stmt->execute()) {
        // Redirect back to manage stocks or show success message
        header("Location: manage_stocks.php?success=1");
        exit();
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Error adding stock: " . $errorInfo[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock - Food Business System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f5e0b5, #f3d4a0);
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            background-color: #fdf8f1;
        }
        .btn-custom {
            background-color: #4E3B31;
            color: white;
            border-radius: 5px;
        }
        .btn-custom:hover {
            background-color: #b3752b;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <h2 class="text-center mb-4">Add New Stock</h2>
                <form action="add_stock.php" method="post">
                    <div class="mb-3">
                        <label for="item" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="item" name="item" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit_price" class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price" required>
                    </div>
                    <button type="submit" class="btn btn-custom">Add Stock</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
