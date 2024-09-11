<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: Stafflogin.php");
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

// Initialize variables
$first_name = "";
$last_name = "";
$profile_picture = "default.png"; // Default profile picture
$total_students = 0;
$total_attendance = 0;
$average_attendance = 0;
$most_attended_module = "";
$modules_result = []; // Initialize as empty array

// Get staff details from database
$staffNo = $_SESSION['staffNo'];
$stmt = $conn->prepare("SELECT first_name, last_name, profile_picture FROM lecturers WHERE staffNo = ?");
$stmt->bind_param("s", $staffNo);
$stmt->execute();
$staff_result = $stmt->get_result();
$staff_details = $staff_result->fetch_assoc();

if ($staff_details) {
    $first_name = $staff_details['first_name'];
    $last_name = $staff_details['last_name'];
    $profile_picture = !empty($staff_details['profile_picture']) ? $staff_details['profile_picture'] : $profile_picture;
}

$staffNo = $_SESSION['staffNo'];
$sql = "SELECT m.module_name, m.module_code 
        FROM modules m 
        JOIN lecturer_modules lm ON m.module_id = lm.module_id 
        WHERE lm.lecturer_id = 4";
$result = $conn->query($sql);

// Fetch total number of students
$stmt = $conn->prepare("SELECT COUNT(*) FROM students");
$stmt->execute();
$stmt->bind_result($total_students);
$stmt->fetch();
$stmt->close();

// Initialize module statistics
$total_students_attending = 0; // Accumulate total students attending

if ($modules_result && $modules_result->num_rows > 0) {
    // Fetch attendance statistics for each module
    while ($module = $modules_result->fetch_assoc()) {
        $moduleName = $module['module_name'];
        
        // Count distinct students for each module
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT student_number) FROM attendance WHERE module_name = ?");
        $stmt->bind_param("s", $moduleName);
        $stmt->execute();
        $stmt->bind_result($studentCount);
        $stmt->fetch();
        $total_students_attending += $studentCount;
        $stmt->close();
    }
}

// Fetch total attendance entries
$stmt = $conn->prepare("SELECT COUNT(*) FROM attendance");
$stmt->execute();
$stmt->bind_result($total_attendance_entries);
$stmt->fetch();
$total_attendance = $total_attendance_entries;
$stmt->close();

// Fetch most attended module
$stmt = $conn->prepare("SELECT module_name, COUNT(*) as attendance_count FROM attendance GROUP BY module_name ORDER BY attendance_count DESC LIMIT 1");
$stmt->execute();
$stmt->bind_result($most_attended_module, $most_attended_count);
$stmt->fetch();
$stmt->close();

// Calculate average attendance
if ($total_students > 0) {
    // Assuming a default of 31 days for attendance records
    $total_days = 31;
    $average_attendance = $total_students > 0 ? round(($total_attendance / ($total_students * $total_days)) * 100, 2) : 0;
}

// Close database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
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
    color: white;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed; /* Fix header to the top */
    width: 100%;
    top: 0;
    left: 0;
    background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
    z-index: 1000; /* Make sure it stays above other content */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
}

        header h1 {
            font-family: "Oswald", sans-serif;
            font-size: 24px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar {
    background: linear-gradient(120deg, #122B40, #446CB3);
    width: 220px;
    padding-top: 60px; /* Adjust to fit below the header */
    display: flex;
    flex-direction: column;
    align-items: center;
    position: fixed; /* Fix sidebar to the left */
    top: 100px; /* Adjust to fit below the header */
    left: 0;
    bottom: 0; /* Stretch to the bottom */
    height: calc(100vh - 50px); /* Adjust height to fit below header */
    overflow-y: auto; /* Enable scrolling */
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
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
    margin-left: 220px; /* Adjust for sidebar width */
    margin-top: 130px; /* Adjust for header height */
    padding: 10px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

        main {
            display: flex;
            flex: 1;
        }

        .dashboard {
            background-color: #f0f2f5;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    text-align: center;
    
}

        .dashboard h2 {
            font-size: 24px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .dashboard-stats {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .stat-item {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            
          
        }

        .stat-item h3 {
            font-size: 18px;
            color: #122B40;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 24px;
            color: #446CB3;
        }

        

        h2 {
            font-size: 20px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .module-list {
            background-color: #f0f2f5;
    padding: 10px;
    margin-top: 10px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

        .module-list h2 {
            margin-bottom: 20px;
            color: #122B40;
        }

        .module-list ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .module-list ul li {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: white;
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
    margin: 0;
    list-style: none;
}

.module-list ul li a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    text-decoration: none;
    color: inherit;
    border-radius: 50%;
    background: white;
    transition: background 0.3s ease, transform 0.3s ease;
}

.module-list ul li a:hover {
    background: linear-gradient(120deg, #122B40, #446CB3);
    color: white;
    transform: scale(1.1);
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
            margin-right: 20px;
            margin-left: 60px;
            overflow: hidden;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
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
            padding: 10px;
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
        </style>
</head>
<body>
    <header>
        <div class="profile-header">
            <div class="profile-picture">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            </div>
            <h1>Welcome, <?php echo htmlspecialchars($first_name . " " . $last_name); ?>!</h1>
        </div>
    </header>
    <main>
        <div class="sidebar">
            <a href="Module.php">Overall Attendance</a>
            <a href="Report.php">Student Report</a>
            <a href="ViewUploads.php">View Uploads</a>
            <a href="LecturerProfile.php">Update Profile</a>
            <a href="http://localhost/StudentAttends/index.php">Logout</a>
        </div>
        <div class="content">
            <div class="dashboard">
                <h2>Dashboard Statistics</h2>
                <div class="dashboard-stats">
                    <div class="stat-item">
                        <h3>Total Students</h3>
                        <p><?php echo number_format($total_students); ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>Total Attendance Entries</h3>
                        <p><?php echo number_format($total_attendance); ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>Average Attendance</h3>
                        <p><?php echo number_format($average_attendance, 2); ?>%</p>
                    </div>
                    
                </div>
            </div>
            <div class="module-list">
    <h2>Modules Lecturing & Moderating</h2>
    <ul>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<li><a href="getModuleDetails.php?module_code=' . htmlspecialchars($row['module_code']) . '" class="module-circle">' . htmlspecialchars($row['module_name']) . '<br>' . htmlspecialchars($row['module_code']) . '</a></li>';
        }
    } else {
        echo "<li><span class='module-circle'>No modules selected</span></li>";
    }
    ?>
    </ul>
</div>

        </div>    
    </main>
    <footer>
       
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.module-list a').forEach(function(moduleLink) {
            moduleLink.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default link behavior
                
                const moduleCode = new URLSearchParams(this.getAttribute('href')).get('module_code');
                
                // Make AJAX request to fetch module details
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'getModuleDetails.php?module_code=' + moduleCode, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        
                        // Update DOM elements with the fetched data
                        document.getElementById('total-enrolled-students').textContent = response.student_count;
                        document.getElementById('attendance-count').textContent = response.attendance_count;
                        document.getElementById('average-attendance').textContent = response.average_attendance + '%';
                    } else {
                        console.error('Failed to fetch module details.');
                    }
                };
                xhr.send();
            });
        });
    });
</script>

</body>
</html>
