<?php
session_start();
include 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $about = isset($_POST['about']) ? $_POST['about'] : '';

    // Update the "About You" information in the database
    $sql = "UPDATE users SET about = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ss", $about, $username);
        $stmt->execute();
        header("Location: profile.php"); // Redirect to the profile page after updating
        exit();
    } else {
        die("Database error: " . $conn->error);
    }
}

// Fetch the current "About You" information
$sql = "SELECT about FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About You</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6e6e6; /* Soft gray background */
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #f3f3f3; /* Light neutral background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50; /* Darker text */
            margin-bottom: 20px;
            text-align: center; /* Center the title */
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical; /* Allow vertical resizing */
            font-size: 16px; /* Increase font size for better readability */
        }
        button {
            background-color: #3498db; /* Neutral blue */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            width: 100%; /* Make button full-width */
            font-size: 16px; /* Increase font size for better readability */
        }
        button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }
        /* Optional: Responsive adjustments */
        @media (max-width: 600px) {
            .container {
                padding: 15px; /* Adjust padding on smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit About You</h1>
        <form action="" method="POST">
            <textarea name="about" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['about']); ?></textarea>
            <button type="submit">Update About</button>
        </form>
    </div>
</body>
</html>
