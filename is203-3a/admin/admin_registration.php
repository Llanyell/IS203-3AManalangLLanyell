<?php
session_start();
include '../database.php'; // Ensure this path is correct

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim inputs to remove any leading/trailing spaces
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin'; // Set the role to admin

    // Check if the username or email is already taken
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param('ss', $username, $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color:red;'>Username or email already exists!</p>";
    } else {
        // Insert the new admin user
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $first_name, $last_name, $username, $email, $password, $role);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Admin registration successful! <a href='../login.php'>Login here</a></p>";
        } else {
            echo "<p style='color:red;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }
    }
}
$conn->close(); // Close the database connection if it's defined
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6e6e6; /* Same background as the dashboard */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .registration-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333; /* Dark color for headers */
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 12px;
            background-color: #7f8c8d; /* Consistent button color */
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #6c7a89; /* Darker shade on hover */
        }
        a {
            display: block;
            margin-top: 10px;
            color: #e84118; /* Same link color as the dashboard */
            text-decoration: none;
        }
        a:hover {
            color: #c0392b; /* Darker shade for hover effect */
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h2>Admin Registration</h2>
        <form method="POST" action="">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <a href="../login.php">Already have an account? Login here.</a>
    </div>
</body>
</html>
