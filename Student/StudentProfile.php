<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: studentlogin.php");
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

// Get student details from database
$studentNumber = $_SESSION['studentNumber'];
$stmt = $conn->prepare("SELECT * FROM students WHERE studentNumber = ?");
$stmt->bind_param("s", $studentNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    // Handle case where student details are not found (optional)
    $student = [];
}

$stmt->close();

// Update student details in the database if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $cellphone = $_POST['cellphone'];
    $course = $_POST['course'];
    $year_of_study = $_POST['year_of_study'];
    $gender = $_POST['gender'];

    // Handle profile picture upload
    $profile_picture = $student['profile_picture']; // Default to existing profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $profile_picture = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    // Update profile details in database
    $stmt = null;
    if ($profile_picture) {
        $stmt = $conn->prepare("UPDATE students SET email = ?, cellphone = ?, course = ?, year_of_study = ?, gender = ?, profile_picture = ? WHERE studentNumber = ?");
        $stmt->bind_param("sssssss", $email, $cellphone, $course, $year_of_study, $gender, $profile_picture, $studentNumber);
    } else {
        $stmt = $conn->prepare("UPDATE students SET email = ?, cellphone = ?, course = ?, year_of_study = ?, gender = ? WHERE studentNumber = ?");
        $stmt->bind_param("ssssss", $email, $cellphone, $course, $year_of_study, $gender, $studentNumber);
    }

    if ($stmt->execute()) {
        $_SESSION['update_message'] = "Profile updated successfully.";
        // Refresh student data after update
        $stmt = $conn->prepare("SELECT * FROM students WHERE studentNumber = ?");
        $stmt->bind_param("s", $studentNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
        } else {
            $student = []; // Handle case where data cannot be fetched (optional)
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
    <title>Student Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            animation: pageFlipIn 1s ease-out;
            padding: 80px 20px 20px 0px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
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
            padding: 20px;
            display: flex;
            justify-content: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 16px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        .back-button {
            position: absolute;
            top: 50%;
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
            color: #122B40;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 10px;
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
            transition: background-color 0.3s, transform 0.3s;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }
        .save-button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transform: scale(1.05);
            color: #122B40;
        }
        footer {
            text-align: center;
            padding: 20px 0;
            background: #f4f4f4;
            margin-top: auto;
            background: linear-gradient(120deg, #122B40, #446CB3);
        }

        footer a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
    &nbsp;<h1>Student Profile</h1>
    <a class="back-button" href="StudentHome.php">Back</a>
    </header>
    <div class="container">
        <div class="profile-header">
            <div class="profile-picture">
                <img src="<?php echo $student['profile_picture'] ? $student['profile_picture'] : 'default.png'; ?>" alt="">
            </div>
            <div class="profile-name">
                <h1><?php echo $student['first_name'] . " " . $student['last_name']; ?></h1>
            </div>
        </div>

        <form action="StudentProfile.php" method="post" enctype="multipart/form-data">
            <div class="profile-details">
                <div>
                    <label>Uplaod Picture</label>
                    <input type="file" name="profile_picture" accept="image/*">
                </div>
                <div>
                    <label>Student Number</label>
                    <input type="text" name="studentNumber" value="<?php echo $student['studentNumber']; ?>" readonly>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $student['email']; ?>">
                </div>
                <div>
                    <label>Cellphone</label>
                    <input type="text" name="cellphone" value="<?php echo $student['cellphone']; ?>">
                </div>
                <div>
                    <label>Course</label>
                    <input type="text" name="course" value="<?php echo $student['course']; ?>"readonly>
                </div>
                <div>
                    <label>Year of Study</label>
                    <input type="text" name="year_of_study" value="<?php echo $student['year_of_study']; ?>"readonly>
                </div>
                <div>
                    <label>Gender</label>
                    <input type="text" name="gender" value="<?php echo $student['gender']; ?>" readonly>
                </div>
            </div>
            <div style="text-align: center;">
                <input type="submit" id="save-button" class="save-button" value="Save Changes">
            </div>
        </form>
    </div>
    <footer>
    
    </footer>
</body>
</html>