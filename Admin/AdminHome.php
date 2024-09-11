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

// Initialize variables
$first_name = "";
$last_name = "";
$profile_picture = "default.png"; // Default profile picture
$total_students = 0;
$total_attendance = 0;
$total_staff = 0;
$average_attendance = 0;
$most_attended_module = "";

// Get admin details from database
$staffNumber = $_SESSION['staffNumber'];
$stmt = $conn->prepare("SELECT first_name, last_name, profile_picture FROM administrator WHERE staffNumber = ?");
$stmt->bind_param("s", $staffNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the admin's first name and last name
    $row = $result->fetch_assoc();
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    // Set profile picture if available, otherwise use default
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : $profile_picture;
}

// Fetch newly registered students
$new_students_query = "SELECT first_name, last_name, created_at FROM students ORDER BY created_at DESC LIMIT 5";
$new_students_result = $conn->query($new_students_query);

// You can adjust the query to fetch newly registered lecturers similarly if needed

// Fetch total students
$sql = "SELECT COUNT(*) AS total_students FROM students";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $total_students = $row['total_students'];
}

// Fetch total attendance entries
$sql = "SELECT COUNT(*) AS total_attendance FROM attendance";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $total_attendance = $row['total_attendance'];
}

// Fetch total staff
$sql = "SELECT COUNT(*) AS total_staff FROM lecturers";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $total_staff = $row['total_staff'];
}

// Fetch most attended module
$sql = "SELECT module_name, COUNT(*) AS attendance_count FROM attendance GROUP BY module_name ORDER BY attendance_count DESC LIMIT 1";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $most_attended_module = $row['module_name'];
}

// Calculate average attendance
if ($total_students > 0) {
    $average_attendance = round(($total_attendance / ($total_students * 31)) * 100);
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .welcome {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .new-registrations {
            background-color: #f0f2f5;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    width: 45%;
    text-align: center;
    float: left; /* Float the registrations to the left */
}

.chart-container {
    width: 45%; /* Make the chart take up the remaining width */
    height: 300px; /* Adjust the height as needed */
    float: left; /* Float the chart to the right */
    padding: 10px;
    background-color: #f0f2f5;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex; /* Use flexbox to center the content */
    align-items: center;
    justify-content: center;
}

.chart-container canvas {
    width: 100%; /* Make the canvas take up the full width of its container */
    height: auto; /* Maintain aspect ratio */
}

.registrations-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.registration-item {
    background-color: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: background-color 0.3s ease;
    justify-content: center; /* Center the content horizontally */
}

.registration-item:hover {
    background-color: #f0f2f5;
}

.icon img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.details {
    text-align: center; /* Center text */
}

.dashboard-sections {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: 20px;
}
.registration-item .details {
    display: flex;
    flex-direction: column;
    gap: 5px;
    text-align: center; /* Center the text */
}

.registration-item .details p {
    margin: 0;
    font-size: 16px;
    color: #122B40;
}

.registration-item .details p strong {
    font-weight: bold;
    color: #446CB3;
}

.registration-item .icon {
    font-size: 40px;
    color: #446CB3;
}
.new-registrations, .chart-container {
    flex: 1; /* Ensure both take up equal space */
}




        h2 {
            font-size: 20px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
            <a href="Students.php">Students</a>
            <a href="Staff.php">Staff</a>
            <a href="Classes.php">Modules</a>
            <a href="http://localhost/StudentAttends/Lecturer/Report.php">Reports</a>
            <a href="AddUser.php">Add User</a>
            <a href="AdminProfile.php">Update Profile</a>
            <a href="http://localhost/StudentAttends/splash.php">Logout</a>
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
                <h3>Total Staff</h3>
                <p><?php echo number_format($total_staff); ?></p>
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

    <!-- New parent container for the new students and chart sections -->
    <div class="dashboard-sections">
        <div class="new-registrations">
            <h2><i class="fas fa-user-plus"></i> New Students</h2>
            <div class="registrations-list">
                <?php
                if ($new_students_result->num_rows > 0) {
                    while ($student = $new_students_result->fetch_assoc()) {
                        echo '<div class="registration-item">';
                        echo '<div class="icon"><img src="default.png" alt="Account Icon"></div>'; // Account icon
                        echo '<div class="details">';
                        echo '<p><strong>Name:</strong> ' . htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) . '</p>';
                        echo '<p><strong>Registration Date:</strong> ' . htmlspecialchars($student['created_at']) . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No new registrations.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Pie chart container -->
        <div class="chart-container">
            <canvas id="studentChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Data for the pie chart (this is just an example, replace with actual data)
    const studentData = {
        labels: [
            'DIPICT',
            'HICT',
            'BICT'
        ],
        datasets: [{
            label: 'Students',
            data: [14, 10,8 ], // Replace with actual data from your database
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)'
            ],
            hoverOffset: 4
        }]
    };

    // Config for the pie chart
    const config = {
        type: 'pie',
        data: studentData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Daily Average Attendance Taken'
                }
            }
        },
    };

    // Render the chart
    const studentChart = new Chart(
        document.getElementById('studentChart'),
        config
    );
</script>

            </div>
        
                </ul>
            </div>
        </div>
    </main>
    
</body>
</html>
