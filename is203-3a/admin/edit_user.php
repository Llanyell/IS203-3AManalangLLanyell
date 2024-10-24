<?php
session_start();
include '../database.php'; // Correct path to your database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role']; // admin or user

    // Update the user
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->bind_param('sssi', $username, $email, $role, $id);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>User updated successfully!</p>";
        header("Location: dashboard.php"); // Redirect back to the dashboard
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}

// Fetch the user details to pre-fill the form
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    header("Location: dashboard.php"); // Redirect if no ID provided
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #74b9ff, #a29bfe);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #2d3436;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 300px;
        }

        input, select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        button {
            padding: 10px;
            background-color: #6c5ce7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #341f97;
        }

        a {
            margin-top: 10px;
            color: #0984e3;
            text-decoration: none;
        }

        a:hover {
            color: #0652dd;
        }
    </style>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <select name="role">
            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
        <button type="submit">Update User</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
