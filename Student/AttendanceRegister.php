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

// Fetch student's enrolled modules
$studentNumber = $_SESSION['studentNumber'];
$sqlModules = "SELECT m.module_id, m.module_name FROM modules m
               JOIN student_modules sm ON m.module_id = sm.module_id
               WHERE sm.student_number = ?";
$stmtModules = $conn->prepare($sqlModules);
$stmtModules->bind_param("s", $studentNumber);
$stmtModules->execute();
$resultModules = $stmtModules->get_result();

// Array to store enrolled module IDs
$enrolledModules = array();
while ($rowModule = $resultModules->fetch_assoc()) {
    $enrolledModules[] = $rowModule['module_id'];
}

// Ensure there are enrolled modules to fetch timetable for
if (count($enrolledModules) > 0) {
    $placeholders = implode(',', array_fill(0, count($enrolledModules), '?'));
    $sqlTimetable = "SELECT t.module_name, t.day_of_week, t.start_time, t.end_time 
                     FROM timetable t
                     JOIN modules m ON t.module_name = m.module_name
                     WHERE m.module_id IN ($placeholders)";
    $stmtTimetable = $conn->prepare($sqlTimetable);
    $stmtTimetable->bind_param(str_repeat('i', count($enrolledModules)), ...$enrolledModules);
    $stmtTimetable->execute();
    $resultTimetable = $stmtTimetable->get_result();

    $timetable = array();
    if ($resultTimetable->num_rows > 0) {
        while ($rowTimetable = $resultTimetable->fetch_assoc()) {
            $timetable[] = $rowTimetable;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Timetable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            animation: pageFlipIn 0.7s ease-out;
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

        .timetable-container {
            width: 100%;
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: bounce-in 0.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        h1 {
            text-align: center;
            font-size: 24px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            animation: bounce-in 2s ease-in-out;
        }

        .day {
            background: #f0f2f5;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            min-height: 150px;
            position: relative;
        }

        .day h2 {
            font-size: 20px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .event {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: #fff;
            padding: 5px;
            border-radius: 4px;
            margin-top: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .event:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transform: scale(1.05);
            color: #122B40;
        }

        @media (max-width: 768px) {
            .calendar {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .calendar {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .timetable-container {
                flex-direction: column;
                align-items: center;
            }
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 1.5em;
        }

        .mark-attendance-btn {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .mark-attendance-btn:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }

        .actions {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .actions a, .actions .close-modal-btn {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }

        .actions a:hover, .actions .close-modal-btn:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }

        .close-modal-btn {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .close-modal-btn:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
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
        }
        </style>
</head>
<body>
    <header>
        &nbsp;<h1>Attendance Register</h1>
        <a class="back-button" href="StudentHome.php">Back</a>
    </header>

    <div class="timetable-container">
        <h1>Student Timetable</h1>
        <div class="calendar">
            <?php 
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                foreach ($days as $day): 
            ?>
                <div class="day">
                    <h2><?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <?php 
                        foreach ($timetable as $row): 
                            if ($row['day_of_week'] == $day): 
                    ?>
                        <div class="event" data-module="<?php echo htmlspecialchars($row['module_name'], ENT_QUOTES, 'UTF-8'); ?>" data-start-time="<?php echo htmlspecialchars($row['start_time'], ENT_QUOTES, 'UTF-8'); ?>" data-end-time="<?php echo htmlspecialchars($row['end_time'], ENT_QUOTES, 'UTF-8'); ?>">
                            <strong><?php echo htmlspecialchars($row['module_name'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                            <?php echo htmlspecialchars($row['start_time'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($row['end_time'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php 
                            endif;
                        endforeach; 
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

   

    <div class="modal" id="eventModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2 id="modalModuleName"></h2>
            <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>
            <p><strong>End Time:</strong> <span id="modalEndTime"></span></p>
            <div class="actions">
                <a href="#" class="mark-attendance-btn">Mark Attendance</a>
                <button class="close-modal-btn">Close</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const events = document.querySelectorAll('.event');
        const modal = document.getElementById('eventModal');
        const closeModal = document.getElementById('closeModal');
        const modalModuleName = document.getElementById('modalModuleName');
        const modalStartTime = document.getElementById('modalStartTime');
        const modalEndTime = document.getElementById('modalEndTime');
        const markAttendanceBtn = document.querySelector('.mark-attendance-btn');
        const closeModalBtn = document.querySelector('.close-modal-btn');

        let currentEvent = null;

        events.forEach(event => {
            event.addEventListener('click', () => {
                currentEvent = event; // Save the current event
                const startTime = event.getAttribute('data-start-time');
                const endTime = event.getAttribute('data-end-time');
                
                modalModuleName.textContent = event.getAttribute('data-module');
                modalStartTime.textContent = startTime;
                modalEndTime.textContent = endTime;
                modal.style.display = 'flex';

                // Check if current time is within event time
                updateButtonState(startTime, endTime);
            });
        });

        markAttendanceBtn.addEventListener('click', () => {
            if (currentEvent) {
                const moduleName = currentEvent.getAttribute('data-module');
                const startTime = currentEvent.getAttribute('data-start-time');
                const endTime = currentEvent.getAttribute('data-end-time');
                const studentNumber = '<?php echo $studentNumber; ?>'; // Pass the student number from PHP
                const deviceUsed = detectDevice(); // Call the function to detect device

                // Perform the time check again before marking attendance
                const now = new Date();
                const start = new Date();
                const end = new Date();

                const [startHours, startMinutes] = startTime.split(':').map(Number);
                const [endHours, endMinutes] = endTime.split(':').map(Number);

                start.setHours(startHours, startMinutes, 0, 0);
                end.setHours(endHours, endMinutes, 0, 0);

                if (now >= start && now <= end) {
                    getLocation(function(location) {
                        const [latitude, longitude] = location.split(',');

                        // University of Mpumalanga Coordinates
                        const universityLat = -25.4356; // Decimal degrees
                        const universityLon = 30.9824;  // Decimal degrees
                        const maxDistance = 1; // Set radius in kilometers (e.g., 0.5 km)

                        const distance = calculateDistance(latitude, longitude, universityLat, universityLon);
                        
                        if (distance <= maxDistance) {
                            // AJAX POST request to markAttendance.php
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'MarkAttendance.php', true);
                            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    alert('Attendance Marked for ' + moduleName);
                                    modal.style.display = 'none';
                                } else {
                                    alert('Failed to mark attendance. Please try again.');
                                }
                            };

                            xhr.send('student_number=' + encodeURIComponent(studentNumber) +
                                     '&module_name=' + encodeURIComponent(moduleName) +
                                     '&device_used=' + encodeURIComponent(deviceUsed) +
                                     '&location=' + encodeURIComponent(location));
                        } else {
                            alert('You must be within ' + maxDistance + ' km of the university to mark attendance.');
                        }
                    });
                } else {
                    alert('You can only mark attendance during the class time.');
                }
            }
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        closeModalBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        function detectDevice() {
            const userAgent = navigator.userAgent;
            if (/mobile/i.test(userAgent)) {
                return "Mobile";
            } else if (/tablet/i.test(userAgent)) {
                return "Tablet";
            } else if (/iPad|iPhone|iPod/.test(userAgent)) {
                return "iOS";
            } else if (/Android/.test(userAgent)) {
                return "Android";
            } else {
                return "Desktop";
            }
        }

        function getLocation(callback) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        callback(`${latitude},${longitude}`);
                    },
                    function(error) {
                        console.warn("ERROR(" + error.code + "): " + error.message);
                        callback("Location not available");
                    },
                    {
                        enableHighAccuracy: true, // Ensures the most accurate location possible
                        timeout: 60000, // Increase timeout to 60 seconds for better accuracy
                        maximumAge: 0 // Don't accept cached positions
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
                callback("Location not available");
            }
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in kilometers
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in kilometers
        }

        function updateButtonState(startTime, endTime) {
            const now = new Date();
            const start = new Date();
            const end = new Date();

            const [startHours, startMinutes] = startTime.split(':').map(Number);
            const [endHours, endMinutes] = endTime.split(':').map(Number);

            start.setHours(startHours, startMinutes, 0, 0);
            end.setHours(endHours, endMinutes, 0, 0);

            if (now >= start && now <= end) {
                markAttendanceBtn.disabled = false;
                markAttendanceBtn.style.background = 'linear-gradient(120deg, #122B40, #446CB3)';
                markAttendanceBtn.style.cursor = 'pointer';
            } else {
                markAttendanceBtn.disabled = true;
                markAttendanceBtn.style.background = '#d0d0d0';
                markAttendanceBtn.style.cursor = 'not-allowed';
            }
        }
    });
</script>


</body>
</html>
