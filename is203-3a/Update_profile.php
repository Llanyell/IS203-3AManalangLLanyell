<?php
session_start();
include 'database.php'; // Include your database connection

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new values from the form
    $newFirstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
    $newLastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
    $newEmail = isset($_POST['email']) ? $_POST['email'] : '';
    $newUsername = isset($_POST['username']) ? $_POST['username'] : '';

    // Update profile picture if uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes) && $fileSize < 2000000) { // 2MB limit
            $newFileName = uniqid() . '_' . $fileName;
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Update the profile picture in the database
                $sql = "UPDATE users SET profile_picture = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ss", $newFileName, $username);
                    $stmt->execute();
                } else {
                    die("Database error: " . $conn->error);
                }
            }
        }
    }

    // Update the user's personal information
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, username = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssss", $newFirstname, $newLastname, $newEmail, $newUsername, $username);
        $stmt->execute();

        // Update session username if changed
        $_SESSION['username'] = $newUsername;
    } else {
        die("Database error: " . $conn->error);
    }
}

// Fetch user data to display in the form
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    die("Database error: " . $conn->error);
}

// Set a default image if no profile picture is found
$profilePicture = isset($user['profile_picture']) && $user['profile_picture'] !== '' 
    ? $user['profile_picture'] 
    : 'default-profile.png'; // Ensure this is the correct default image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6e6e6; /* Soft gray background */
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #f3f3f3; /* Light neutral background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50; /* Darker text */
        }
        form {
            margin-bottom: 20px;
        }
        input[type="file"],
        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #3498db; /* Neutral blue */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }
        img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid #3498db; /* Neutral blue */
            object-fit: cover;
        }
        a {
            margin-top: 15px;
            display: inline-block;
            text-decoration: none;
            color: #3498db; /* Neutral link color */
            font-weight: 600;
        }
        a:hover {
            color: #2980b9; /* Darker link color on hover */
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Profile</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">

            <button type="submit">Update Profile</button>
        </form>

        <h2>Your Current Profile Picture</h2>
        <img src="uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Current Profile Picture">
        <a href="profile.php">Back to Profile</a>
    </div>
</body>
</html>
