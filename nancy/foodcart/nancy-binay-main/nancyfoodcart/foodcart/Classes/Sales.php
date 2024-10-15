<?php
class Sales {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all sales records
    public function getSalesRecords() {
        $query = "SELECT sale_date, amount FROM sales ORDER BY sale_date DESC"; // Use 'sale_date' instead of 'date'
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a sales record
    public function addSalesRecord($sale_date, $amount) { // Change parameter name to 'sale_date'
        $query = "INSERT INTO sales (sale_date, amount) VALUES (:sale_date, :amount)"; // Use 'sale_date'
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_date', $sale_date); // Bind the correct parameter
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();
    }

    // Fetch daily sales totals
    public function getDailySales() {
        $query = "SELECT sale_date, SUM(amount) as total_sales FROM sales GROUP BY sale_date ORDER BY sale_date DESC"; // Use 'sale_date'
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
