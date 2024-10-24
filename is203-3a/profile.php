<?php
session_start();
include 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
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
    <title>User Profile</title>
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
            display: flex;
            align-items: flex-start; /* Align items to the start vertically */
        }

        .profile-pic {
            margin-right: 20px; /* Space between the image and the text */
            display: flex;
            flex-direction: column; /* Ensures the picture stays at the top */
        }

        .profile-pic img {
            border-radius: 50%;
            border: 2px solid #7f8c8d; /* Neutral color */
            width: 120px; /* Fixed size for profile picture */
            height: 120px; /* Fixed size for profile picture */
            object-fit: cover; /* Ensures the image covers the space */
        }

        .profile-info {
            margin-left: 20px;
            flex: 1; /* Makes the profile info take remaining space */
        }

        .profile-info h1 {
            margin: 0;
            color: #2c3e50; /* Darker text for better readability */
        }

        .profile-info p {
            margin: 5px 0;
            color: #34495e; /* Gray color for secondary text */
        }

        .purpose {
            margin-top: 20px;
            padding: 15px;
            background-color: #dfe6e9;
            border-radius: 8px;
            color: #2d3436;
            border: 1px solid #ccc; /* Subtle border for separation */
        }

        a {
            margin: 10px 5px;
            text-decoration: none;
            color: #7f8c8d; /* Neutral link color */
            font-weight: 600;
        }

        a:hover {
            color: #34495e; /* Darker link color on hover */
        }

        /* Optional: Styling for the purpose section */
        .purpose h2 {
            margin: 0 0 10px;
            color: #2c3e50; /* Consistent heading color */
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .container {
                flex-direction: column; /* Stack the profile picture and info on smaller screens */
                align-items: center;
                text-align: center;
            }

            .profile-pic {
                margin: 0 0 15px; /* Space below profile picture */
            }

            .profile-info {
                margin-left: 0; /* Remove left margin on smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-pic">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
        </div>
        <div class="profile-info">
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <a href="update_profile.php">Edit Profile</a> | <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="purpose">
        <h2>About You</h2>
        <p><?php echo htmlspecialchars($user['about']) ?: "You haven't added any information about yourself yet."; ?></p>
        <a href="update_about.php">Edit About You</a> <!-- Link to the edit about page -->
    </div>
</body>
</html>
