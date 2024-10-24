<?php
session_start();
include '../database.php'; // Adjust the path if needed

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not an admin
    exit();
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>User deleted successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error deleting user: " . htmlspecialchars($stmt->error) . "</p>";
    }
}

// Handle user update
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Handle profile picture upload
    $profile_picture = $_POST['current_picture']; // Keep the old picture by default

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
        $profile_picture = basename($_FILES['profile_picture']['name']); // Update to new profile picture
    }

    // Update user information
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $username, $email, $profile_picture, $user_id);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>User updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error updating user: " . htmlspecialchars($stmt->error) . "</p>";
    }
}

// Fetch users to display
$result = $conn->query("SELECT id, first_name, last_name, username, email, profile_picture FROM users");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6e6e6; /* Same background as registration */
            padding: 20px;
            color: #444; /* Darker text color */
        }
        h1 {
            color: #333; /* Dark color for headers */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white; /* White background for table */
            border-radius: 10px; /* Rounded corners for table */
            overflow: hidden; /* Rounded corners effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Shadow for table */
        }
        table, th, td {
            border: 1px solid #ddd; /* Light border */
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #7f8c8d; /* Consistent header color */
            color: white; /* Header text color */
        }
        a {
            color: #e84118; /* Red color for delete link */
        }
        a:hover {
            text-decoration: underline; /* Underline on hover */
        }
        button {
            padding: 10px 15px;
            background-color: #7f8c8d; /* Consistent button color */
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: #6c7a89; /* Darker shade on hover */
        }
        img {
            width: 50px; /* Set a fixed size for the profile picture */
            height: 50px;
            border-radius: 50%; /* Make the image circular */
            object-fit: cover; /* Crop the image nicely */
        }
        .update-form {
            display: none; /* Hidden by default */
            margin-top: 20px;
            background: white; /* White background for the form */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Slight shadow for depth */
        }
        .update-form input[type="text"], .update-form input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc; /* Light border for inputs */
            border-radius: 5px; /* Rounded corners */
        }
        .update-form input[type="file"] {
            margin: 10px 0; /* Spacing for file input */
        }
    </style>
    <script>
        function showUpdateForm(id, firstName, lastName, username, email, profilePicture) {
            document.getElementById('user_id').value = id;
            document.getElementById('first_name').value = firstName;
            document.getElementById('last_name').value = lastName;
            document.getElementById('username').value = username;
            document.getElementById('email').value = email;
            document.getElementById('current_picture').value = profilePicture;
            document.getElementById('update-form').style.display = 'block'; // Show the update form
        }

        function printPage() {
            window.print(); // Print the current page
        }
    </script>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <button onclick="window.location.href='admin/admin_registration.php'">Add Admin User</button>
    <button onclick="printPage()">Print</button> <!-- Print Button -->
    
    <h2>User List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Profile Picture</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td>
                        <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default-profile.png'); ?>" alt="Profile Picture">
                    </td>
                    <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <button onclick="showUpdateForm(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name']); ?>', '<?php echo htmlspecialchars($user['last_name']); ?>', '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['profile_picture']); ?>')">Update</button>
                        <form action="" method="GET" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button> <!-- Delete Button -->
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="update-form" id="update-form">
        <h2>Update User</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" id="user_id">
            <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
            <input type="text" name="username" id="username" placeholder="Username" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="hidden" name="current_picture" id="current_picture">
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit" name="update_user">Update User</button>
        </form>
    </div>

    <form action="../logout.php" method="POST" style="display:inline;">
        <button type="submit">Logout</button> <!-- Logout Button -->
    </form>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
