<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: Adminlogin.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studentattends_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get administrator details from database
$staffNumber = $_SESSION['staffNumber'];
$stmt = $conn->prepare("SELECT * FROM administrator WHERE staffNumber = ?");
$stmt->bind_param("s", $staffNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $administrator = $result->fetch_assoc();
} else {
    // Handle case where administrator details are not found (optional)
    $administrator = [];
}

$stmt->close();

// Update administrator details in the database if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $department = $_POST['department'];
    $position = $_POST['position'];

    // Handle profile picture upload
    $profile_picture = $administrator['profile_picture']; // Default to existing profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $profile_picture = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    // Update profile details in database
    if ($profile_picture) {
        $stmt = $conn->prepare("UPDATE administrator SET first_name = ?, last_name = ?, email = ?, contact = ?, department = ?, position = ?, profile_picture = ? WHERE staffNumber = ?");
        $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $contact, $department, $position, $profile_picture, $staffNumber);
    } else {
        $stmt = $conn->prepare("UPDATE administrator SET first_name = ?, last_name = ?, email = ?, contact = ?, department = ?, position = ? WHERE staffNumber = ?");
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $contact, $department, $position, $staffNumber);
    }

    if ($stmt->execute()) {
        $_SESSION['update_message'] = "Profile updated successfully.";
        // Refresh administrator data after update
        $stmt = $conn->prepare("SELECT * FROM administrator WHERE staffNumber = ?");
        $stmt->bind_param("s", $staffNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $administrator = $result->fetch_assoc();
        } else {
            $administrator = []; // Handle case where data cannot be fetched (optional)
        }
    } else {
        $_SESSION['update_message'] = "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
         body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            animation: pageFlipIn 1s ease-out;
        }

        /* Page flip animation CSS */
@keyframes pageFlipIn {
    0% {
        transform: rotateY(-90deg);
        opacity: 0;
    }
    100% {
        transform: rotateY(0deg);
        opacity: 1;
    }
}
        header {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between; 
            text-align: center; /* Center align text within header */
        }
        header h1 {
            margin: 0;
            font-size: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .back-button {
            position: absolute;
            top: 5%;
            left: 20px;
            transform: translateY(-50%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            padding: 10px 20px;
            background: linear-gradient(120deg, #122B40, #446CB3);
            transition: background-color 0.3s, transform 0.3s;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transform: scale(1.05);
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            background-color: #f4f4f4;
            overflow: hidden; /* Ensure circular shape */
        }
        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%; /* Ensure circular shape */
        }
        .profile-name h1 {
            margin: 0;
            font-size: 2em;
            color: #122B40;
        }
        .profile-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .profile-details div {
            flex: 1 1 45%;
            margin-bottom: 20px;
        }
        .profile-details label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #122B40;
        }
        .profile-details input,
        .profile-details p {
            margin: 0;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 350px;
        }
        .profile-details input[type="file"] {
            width: auto; /* Adjust width for file input */
        }
        .profile-details input[readonly] {
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .save-button {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }
        .save-button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
       
    </style>
</head>
<body>
    <header>
    &nbsp;<h1>Administrator Profile</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <div class="container">
        <div class="profile-header">
            <div class="profile-picture">
                <img src="<?php echo $administrator['profile_picture'] ? $administrator['profile_picture'] : 'default.png'; ?>" alt="Profile Picture">
            </div>
            <div class="profile-name">
                <h1><?php echo $administrator['first_name'] . " " . $administrator['last_name']; ?></h1>
            </div>
        </div>

        <form action="Adminprofile.php" method="post" enctype="multipart/form-data">
            <div class="profile-details">
                <div>
                    <label>Upload Picture</label>
                    <input type="file" name="profile_picture" accept="image/*">
                </div>
                <div>
                    <label>Staff Number</label>
                    <input type="text" name="staffNumber" value="<?php echo $administrator['staffNumber']; ?>" readonly>
                </div>
                <div>
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo $administrator['first_name']; ?>">
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo $administrator['last_name']; ?>">
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $administrator['email']; ?>">
                </div>
                <div>
                    <label>Contact</label>
                    <input type="text" name="contact" value="<?php echo $administrator['contact']; ?>">
                </div>
                <div>
                    <label>Department</label>
                    <input type="text" name="department" value="<?php echo $administrator['department']; ?>">
                </div>
                <div>
                    <label>Position</label>
                    <input type="text" name="position" value="<?php echo $administrator['position']; ?>">
                </div>
            </div>
            <div style="text-align: center;">
                <input type="submit" id="save-button" class="save-button" value="Save Changes">
            </div>
        </form>
    </div>
    
</body>
</html>