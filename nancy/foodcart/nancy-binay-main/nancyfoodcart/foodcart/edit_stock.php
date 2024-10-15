<?php
require_once 'classes/database.php';

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

// Check if stock ID is passed via GET request
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch the stock data
    $query = "SELECT * FROM stocks WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $item_id);
    $stmt->execute();
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    // If stock is found, display it in the form
    if ($stock) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update the stock with the new values from the form
            $item = $_POST['item'];
            $quantity = $_POST['quantity'];
            $unit_price = $_POST['unit_price'];
            $total_value = $quantity * $unit_price;

            // Update query
            $updateQuery = "UPDATE stocks 
                            SET item = :item, quantity = :quantity, unit_price = :unit_price, total_value = :total_value, last_updated = NOW()
                            WHERE id = :id";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(':item', $item);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':unit_price', $unit_price);
            $stmt->bindParam(':total_value', $total_value);
            $stmt->bindParam(':id', $item_id);

            if ($stmt->execute()) {
                // Redirect back to manage stocks after successful update
                header("Location: manage_stocks.php?success=1");
                exit();
            } else {
                $error = "Error updating stock.";
            }
        }
    } else {
        $error = "Stock not found.";
    }
} else {
    header("Location: manage_stocks.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet"> <!-- Poppins font -->
    <link href="css/styles.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f5e0b5, #f3d4a0);
            font-family: 'Poppins', sans-serif; /* Apply Poppins font */
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-update {
            background-color: #4E3B31; /* Dark brown color */
            border-color: #4E3B31; /* Match border color */
            color: white; /* White text color */
        }
        .btn-update:hover {
            background-color: #3c2e27; /* Darker shade on hover */
            border-color: #3c2e27; /* Match border color on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Edit Stock</h2>
        
        <!-- Display success or error message -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="item" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="item" name="item" value="<?php echo htmlspecialchars($stock['item']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($stock['quantity']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="unit_price" class="form-label">Unit Price</label>
                <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="<?php echo htmlspecialchars($stock['unit_price']); ?>" required>
            </div>
            <button type="submit" class="btn btn-update w-100">Update Stock</button>
        </form>
    </div>
</body>
</html>
