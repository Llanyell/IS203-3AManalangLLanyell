<?php
session_start();
include '../database.php'; // Include database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT role FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

// Fetch all users from the database
$sql = "SELECT id, username, email, role FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
</head>
<body>
    <h1>Manage Users</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['role']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="../logout.php">Logout</a>
</body>
</html>
