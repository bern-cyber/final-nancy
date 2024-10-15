<?php
class User {
    private $conn;
    private $username;
    private $password;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function loginWithFixedCredentials($username, $password) {
        $fixedCredentials = [
            'username' => 'admin', 
            'password' => 'admin123' 
        ];

        if ($username === $fixedCredentials['username'] && $password === $fixedCredentials['password']) {
            $_SESSION['user_id'] = $username; 
            return true;
        }
        return false;
    }

    public function getUsername() {
        return $this->username;
    }
}
?>
