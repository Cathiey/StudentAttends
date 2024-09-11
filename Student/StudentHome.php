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

$student = []; // Initialize $student variable
$profile_picture = "default.png"; // Default profile picture

// Get student details from database
$studentNumber = $_SESSION['studentNumber'];
$stmt = $conn->prepare("SELECT first_name, last_name, profile_picture FROM students WHERE studentNumber = ?");
$stmt->bind_param("s", $studentNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the staff's first name and last name
    $row = $result->fetch_assoc();
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    // Set profile picture if available, otherwise use default
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : $profile_picture;
}

$studentNumber = $_SESSION['studentNumber'];
$sql = "SELECT m.module_name, m.module_code 
        FROM modules m 
        JOIN student_modules sm ON m.module_id = sm.module_id 
        WHERE sm.student_number = '$studentNumber'";
$result = $conn->query($sql);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
    <style>
      body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
   
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    animation: pageFlipIn 1s ease-out;
        }

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
            
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between; 
        }

        header h1 {
            font-family: "Oswald", sans-serif;
            font-size: 36px;
            color: #122B40;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Add a shadow */
            animation: bounce-in 0.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .sidebar {
            background: linear-gradient(120deg, #122B40, #446CB3);
            width: 220px;
            padding-top: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: sticky;
            top: 0;
            height: auto;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            width: 180px;
        }

        .sidebar a:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transform: scale(1.05);
            color: #122B40;
        }
        .content {
            flex: 1; /* Take remaining space */
            padding: 10px;
            display: flex;
            flex-direction: column;
            
        }
        main {
            display: flex; /* Enable flexbox layout */
            flex: 1; /* Take remaining space */
            
        }
        .welcome {
            background: #f0f2f5;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: bounce-in 1s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        h2 {
            color: #122B40;
            font-size: 20px;
            font-family: 'Arial Black', sans-serif; /* Different font family */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Add a shadow */
        }
        .actions {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .actions a {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            animation: bounce-in 2.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .actions a:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transform: scale(1.05);
            color: #122B40;
        }
        .module-list {
            background: #f0f2f5;
            padding: 10px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: bounce-in 1.1s ease-in-out;
        }

        .module-list h2 {
            color: #122B40;
            font-size: 22px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            text-align : center;
        }

        .module-list ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .module-list ul li {
            margin: 10px;
        }

        .module-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(224, 248, 255, 0.3);
            color: #122B40;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 14px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            line-height: 1.2;
            animation: zoom-in-out 6s ease-in-out infinite;
        }

        .module-circle:hover {
            transform: scale(1.1);
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        @keyframes zoom-in-out {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .profile-picture {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 50px; /* Increased from 20px to 30px */
    margin-left: 50px;
    overflow: hidden; /* Ensure circular shape */
}

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%; /* Ensure circular shape */
        }
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            .profile-picture {
                margin: 0 auto 10px auto;
            }
            .profile-header h1 {
                margin-top: 10px;
            }
        }

        
       
        footer {
            text-align: center;
            padding: 10px 0;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            color: #122B40;
            
        }

        footer p {
            margin: 0;
        }

        footer a {
            color: #E0F8FF;
            text-decoration: none;
            font-weight: bold;
        }
          /* Flip effect */
          .flip-container {
            perspective: 1000px;
            position: relative;
            width: 100%;
            height: 100%;
            
        }

        .flip-card {
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
            position: absolute;
            transform: rotateY(0deg);
            
        }

        .flip-card.flip {
            transform: rotateY(180deg);
        }

        .flip-card .front,
        .flip-card .back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 5px;
        }

        .flip-card .front {
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            padding: 0px;
            z-index: 2;
            transform: rotateY(0deg);
        }

        .flip-card .back {
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            padding: 10px;
            transform: rotateY(180deg);
        }
    </style>
</head>
<body>
    <header>
        <div class="profile-header">
            <div class="profile-picture">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <h1>Student Attends</h1>

        </div>
    </header>
    </header>
    <main>
    <div class="sidebar">
            <a href="Modules.php">Select Modules</a>
            <a href="Uploads.php">Upload</a>
            <a href="StudentProfile.php">Update Profile</a>
            <a href="#">Settings</a>
            <a href="http://localhost/StudentAttends/splash.php">Logout</a>
        </div>
        <div class="content">
        <div class="flip-container">
                <div class="flip-card">
                    <div class="front">
        <div class="welcome">
            <h2>Hello! <?php echo $first_name . " " . $last_name; ?></h2>
            <p>"Welcome to Student Attends – track your progress, own your success!"</p>
        </div>
        <div class="actions">
        <a href="AttendanceRegister.php">Take Attendance</a>
        <a href="ViewAttendance.php">View Attendance</a>
        </div>
   
    
        <div id="search-results"></div>
        <section class="module-list">
    <h2>Your Selected Modules</h2>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<li><div class="module-circle">' . $row['module_name'] . '<br>' . $row['module_code'] . '</div></li>';
            }
        } else {
            echo "<li>No modules selected</li>";
        }
        ?>
    </ul>
</section>

        </div>
                </div>
            </div>
        </div>
    </main>
    <footer>

    </footer>
</body>
</html>