<?php
session_start();
include '../is203-3a/database.php'; // Adjusted path to the correct directory

// Initialize an error message variable
$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); // Trimmed input for cleanliness
    $password = $_POST['password'];

    // Check if the username exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password and check user role
    if ($user && password_verify($password, $user['password'])) {
        // Login successful, store user info in session
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php"); // Redirect admin to dashboard
        } else {
            header("Location: index.php"); // Redirect regular user to their profile
        }
        exit(); // Ensure no further code is executed after redirect
    } else {
        $error_message = "Invalid username or password!";
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #e6e6e6;  /* Soft gray background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #f3f3f3;  /* Light neutral background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #444;  /* Dark gray text */
        }
        input {
            width: 85%; /* Center inputs within the container */
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d1d1d1;
            border-radius: 5px;
            background-color: #f9f9f9; /* Light background for inputs */
            color: #444; /* Dark gray text */
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #7f8c8d; /* Gray focus border */
            outline: none;
            box-shadow: 0 0 8px rgba(127, 140, 141, 0.5);
        }
        button {
            padding: 10px;
            background-color: #7f8c8d; /* Neutral button color */
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #6c7a89; /* Darker shade on hover */
        }
        a {
            display: block;
            margin-top: 10px;
            color: #7f8c8d; /* Neutral link color */
            text-decoration: none;
        }
        a:hover {
            color: #5a6a6d; /* Darker shade on hover */
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <button onclick="window.location.href='admin/admin_registration.php'" style="background-color: #7f8c8d; color: white; border: none; border-radius: 5px; cursor: pointer; padding: 10px; margin-top: 10px; width: 100%;">Register as Admin</button>
        <a href="registration.php">Don't have an account? Register as a user here.</a>
    </div>
</body>
</html>

