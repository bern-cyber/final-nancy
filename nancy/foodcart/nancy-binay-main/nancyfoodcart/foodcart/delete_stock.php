<?php
// delete_stock.php
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

    // Delete the stock from the database
    $query = "DELETE FROM stocks WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $item_id);

    if ($stmt->execute()) {
        // Redirect back to manage stocks after successful deletion
        header("Location: manage_stocks.php");
        exit();
    } else {
        echo "Error deleting stock.";
    }
} else {
    header("Location: manage_stocks.php");
    exit();
}
?>
