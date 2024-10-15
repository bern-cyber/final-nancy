<?php
class Dashboard {
    // No more role needed in the constructor
    public function __construct() {
        // Constructor can remain empty or have some other logic if needed
    }

    // Display stock levels for all users
    public function showStockLevels($stockData) {
        echo "<h3>Stock Levels</h3>";
        echo "<ul>";
        foreach ($stockData as $item => $quantity) {
            echo "<li>$item: $quantity units</li>";
        }
        echo "</ul>";
    }

    // Display sales for all users
    public function showSales($salesToday) {
        echo "<h3>Today's Sales</h3>";
        echo "<p>₱" . number_format($salesToday) . "</p>";
    }

    // Display expenses for all users
    public function showExpenses($expensesToday) {
        echo "<h3>Today's Expenses</h3>";
        echo "<p>₱" . number_format($expensesToday) . "</p>";
    }

    // Display profit for all users (or you could restrict it to admins if needed)
    public function showProfit($profitToday) {
        echo "<h3>Today's Profit</h3>";
        echo "<p>₱" . number_format($profitToday) . "</p>";
    }

    // Show stock management option for all users (or keep this specific for stock personnel)
    public function showStockManagementOption() {
        echo "<h3>Manage Stock Levels</h3>";
        echo "<p><a href='manage_stocks.php' class='btn btn-primary'>Update Stock Levels</a></p>";
    }
}
?>
