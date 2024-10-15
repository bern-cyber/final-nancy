<?php
require 'classes/User.php';
require 'classes/database.php';

session_start(); // Start the session

// Initialize the database connection
$database = new Database();
$conn = $database->getConnection();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User($conn);

    // Attempt to login with fixed credentials (no role required)
    if ($user->loginWithFixedCredentials($username, $password)) {
        header("Location: dashboard.php"); // Redirect to dashboard on successful login
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - Food Business System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Poppins Font -->
    <style>
        body {
            background: linear-gradient(to right, #f5e0b5, #f3d4a0); /* Gradient background */
            font-family: 'Poppins', sans-serif; /* Use Poppins font */
        }

        .bg-login {
            background: rgba(255, 255, 255, 0.9); /* Light background for login container */
        }

        .form-container {
            background-color: #fff; /* White background for form */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        .alert {
            margin-top: 20px; /* Spacing for error message */
        }

        .btn-custom {
            background-color: #4E3B31; /* Dark brown color */
            border-color: #4E3B31; /* Match border color */
            color: white; /* White text color */
            font-weight: bold; /* Bold text */
        }
        
        .btn-custom:hover {
            background-color: #3c2e27; 
            border-color: #3c2e27; 
        }

        .welcome-text {
            color: #4E3B31; 
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="form-container text-center shadow-lg p-4 rounded">
        <h2 class="mb-4 welcome-text">Welcome Back!</h2> 
        <img src="css/chicken.png" alt="Food Business Logo" class="mb-3" width="100">


        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <button type="submit" class="btn btn-lg btn-custom w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
