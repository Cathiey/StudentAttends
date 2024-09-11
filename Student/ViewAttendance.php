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

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentNumber = $_SESSION['studentNumber'];

// Fetch attended classes for registered modules
$sqlAttendance = "
    SELECT m.module_name,
           COUNT(a.id) AS classes_attended
    FROM modules m
    LEFT JOIN timetable t ON m.module_name = t.module_name
    LEFT JOIN attendance a ON t.module_name = a.module_name
    AND a.student_number = ?
    WHERE a.student_number IS NOT NULL
    GROUP BY m.module_name";

$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->bind_param("s", $studentNumber);
$stmtAttendance->execute();
$resultAttendance = $stmtAttendance->get_result();

$attendanceData = array();
while ($row = $resultAttendance->fetch_assoc()) {
    $row['total_classes'] = 31;  // Set total number of classes to 31
    $attendanceData[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance Progress</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .content {
            width: 60%;
            max-width: 60%;
            margin: auto;
            margin-top: 10px;
            margin-bottom: 10px;
            padding: 10px;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex-grow: 1;
            color: #122B40;
            animation: bounce-in 1s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        footer {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(120deg, #122B40, #446CB3);
            margin-top: auto;
        }
        footer a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        canvas {
            margin-top: 10px;
            max-width: 100%;
            background: #f0f2f5;
            animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            header h1 {
                font-size: 14px;
                text-align: left;
            }
            .back-button {
                top: 10px;
                left: 10px;
                transform: none;
                margin-bottom: 10px;
            }
            footer a {
                display: block;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <header>
    <a class="back-button" href="StudentHome.php">Back</a>
        &nbsp;<h1>Attendance Progress</h1>
    </header>

    <div class="content">
        <h2>Your Attendance Progress</h2>
        <canvas id="attendanceChart"></canvas>
    </div>

    <footer>
        
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceData = <?php echo json_encode($attendanceData); ?>;

            const labels = attendanceData.map(data => data.module_name);
            const classesAttended = attendanceData.map(data => parseInt(data.classes_attended, 10));
            const totalClasses = attendanceData.map(() => 31); // Total classes fixed at 31
            const attendancePercentage = attendanceData.map((data, index) => 
                totalClasses[index] > 0 ? (classesAttended[index] / totalClasses[index]) * 100 : 0
            );

            const data = {
                labels: labels,
                datasets: [{
                    label: 'Attendance Percentage (%)',
                    data: attendancePercentage,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.raw.toFixed(2) + '%';
                                }
                            }
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
</body>
</html>