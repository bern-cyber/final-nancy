<?php
class Expense {
    private $conn;
    private $table_name = "expenses"; // Change as per your database table

    // Properties to hold the expense data
    private $date;
    private $amount;
    private $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to fetch all expense records
    public function getExpenseRecords() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Fetching the data as associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return array of associative arrays
    }

    // Method to add a new expense record
    public function addExpenseRecord($date, $amount, $description) {
        $query = "INSERT INTO " . $this->table_name . " (date, amount, description) VALUES (:date, :amount, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':description', $description);

        // Execute and return the success status
        return $stmt->execute();
    }

    // Method to get expenses by specific date
    public function getExpensesByDate($date) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE date = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        // Fetching the data as associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return array of associative arrays
    }
    
    // Getter methods
    public function getDate() {
        return $this->date; 
    }

    public function getAmount() {
        return $this->amount; 
    }

    public function getDescription() {
        return $this->description; 
    }

    // Setter methods to initialize an expense object (if needed)
    public function setDate($date) {
        $this->date = $date;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
}
