<?php
session_start();
include 'database.php'; // Include your database connection

// Handle sign-up form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; // Default role

    // Check if the username or email is already taken
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param('ss', $username, $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already exists!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $first_name, $last_name, $username, $email, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Sign-up successful!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #e6e6e6;  /* Soft gray background */
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .signup-container {
            background-color: #f3f3f3;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
            color: #444;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            width: 85%; /* Center inputs within the container */
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #d1d1d1;
            border-radius: 8px;
            background-color: #f9f9f9;
            color: #444;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        input::placeholder {
            color: #aaa;
        }

        input:focus {
            border-color: #7f8c8d;
            box-shadow: 0 0 8px rgba(127, 140, 141, 0.5);
            outline: none;
        }

        button {
            width: 85%;
            padding: 14px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            background-color: #7f8c8d;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #6c7a89;
        }

        a {
            margin-top: 15px;
            color: #7f8c8d;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #5a6a6d;
        }

        @media (max-width: 500px) {
            .signup-container {
                width: 90%;
                padding: 30px;
            }

            input, button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create an Account</h2>
        <form method="POST" action="">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <a href="login.php">Already have an account? Login here.</a>
    </div>
</body>
</html>
