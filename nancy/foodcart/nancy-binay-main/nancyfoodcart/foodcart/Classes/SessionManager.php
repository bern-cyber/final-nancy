<?php
class SessionManager {
    public function __construct() {
        // Start the session
        session_start();
    }

    // Check if the user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']); // Adjust 'user_id' based on your session structure
    }
    

    // Redirect to a specified page
    public function redirect($url) {
        header("Location: $url");
        exit();
    }
}
?>
