<?php
session_start();
include 'database.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT first_name, last_name, email, profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6e6e6; /* Same background as registration */
            padding: 20px;
            color: #444; /* Darker text color */
        }

        .dashboard-container {
            background-color: white; /* White background for the dashboard */
            max-width: 400px;
            margin: auto;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333; /* Dark color for headers */
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #7f8c8d; /* Border color consistent with your design */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        p {
            color: #636e72;
            margin-bottom: 30px;
            font-size: 16px;
        }

        a {
            display: inline-block;
            margin: 10px 0;
            color: #e84118; /* Same link color as registration */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #c0392b; /* Darker shade for hover effect */
        }

        button {
            padding: 12px 20px;
            background-color: #7f8c8d; /* Consistent button color */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #6c7a89; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!</h1>
        <!-- Debugging Output -->
        <p>Profile Picture Path: <?php echo htmlspecialchars($user['profile_picture'] ?: 'images/default-profile.png'); ?></p>
        <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'default-profile.png'); ?>" alt="Profile Picture">
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <a href="update_profile.php">Edit Profile</a>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit">Logout</button> <!-- Logout Button -->
        </form>
    </div>
</body>
</html>
