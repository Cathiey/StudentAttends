<?php
// Start the session for PDF export
session_start();

// Include TCPDF library
require_once __DIR__ . '/TCPDF-main/tcpdf.php';

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

// Get module name from URL
$module_name = isset($_GET['module_name']) ? $_GET['module_name'] : '';

// Initialize variables
$attendance_data = [];
$selected_date = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : date('Y-m-d');
$student_attendance = [];
$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

// Initialize variables for statistics
$total_attendance = 0;
$unique_students = 0;
$students_at_risk = [];
$attendance_threshold = 75; // Set your threshold percentage here

// Handle PDF export request
if (isset($_POST['export_pdf'])) {
    // Fetch attendance data for the PDF
    $stmt = $conn->prepare("SELECT attendance.student_number, students.first_name, students.last_name, attendance.marked_date, attendance.device_used, attendance.location
                            FROM attendance 
                            JOIN students ON attendance.student_number = students.studentNumber 
                            WHERE attendance.module_name = ? AND DATE(attendance.marked_date) = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $module_name, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare data for PDF
    $pdf_data = [];
    while ($row = $result->fetch_assoc()) {
        $pdf_data[] = $row;
    }
    $stmt->close();

    // Create PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Student Attends');
    $pdf->SetTitle('Attendance Report');
    $pdf->SetSubject('Attendance Report');
    $pdf->SetKeywords('TCPDF, PDF, attendance, report');

    $pdf->SetHeaderData('', 0, 'Attendance Report', 'Module: ' . htmlspecialchars($module_name) . ' | Date: ' . htmlspecialchars($selected_date));
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Table Header
    $html = '
    <table border="1" cellpadding="4">
        <thead>
            <tr style="background-color: #d3e6f9; font-weight: bold;">
                <th>Student Number</th>
                <th>Student Name</th>
                <th>Date</th>
                <th>Device Used</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>';

    // Table Body
    foreach ($pdf_data as $attendance) {
        $html .= '<tr>
            <td>' . htmlspecialchars($attendance['student_number']) . '</td>
            <td>' . htmlspecialchars($attendance['first_name'] . ' ' . $attendance['last_name']) . '</td>
            <td>' . htmlspecialchars($attendance['marked_date']) . '</td>
            <td>' . htmlspecialchars($attendance['device_used']) . '</td>
            <td>' . htmlspecialchars($attendance['location']) . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('Attendance_Report.pdf', 'D');
    exit;
}

// Fetch attendance data and calculate statistics if module_name is set
if ($module_name) {
    // Get the total number of sessions for the module
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT DATE(marked_date)) AS total_sessions 
                            FROM attendance 
                            WHERE module_name = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $module_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_sessions = $result->fetch_assoc()['total_sessions'];
    $stmt->close();

    // Fetch attendance data for all students
    $stmt = $conn->prepare("SELECT attendance.student_number, students.first_name, students.last_name, COUNT(attendance.student_number) AS attended_sessions 
                            FROM attendance 
                            JOIN students ON attendance.student_number = students.studentNumber 
                            WHERE attendance.module_name = ? 
                            GROUP BY attendance.student_number");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $module_name);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendance_percentage = ($row['attended_sessions'] / $total_sessions) * 100;

        // Check if the student is at risk
        if ($attendance_percentage < $attendance_threshold) {
            $students_at_risk[] = [
                'student_number' => $row['student_number'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'attendance_percentage' => round($attendance_percentage, 2)
            ];
        }
    }
    $stmt->close();

    // Calculate attendance statistics for the selected date
    $stmt = $conn->prepare("SELECT attendance.student_number, students.first_name, students.last_name, attendance.marked_date, attendance.device_used, attendance.location
                            FROM attendance 
                            JOIN students ON attendance.student_number = students.studentNumber 
                            WHERE attendance.module_name = ? AND DATE(attendance.marked_date) = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $module_name, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $student_attendance[] = [
            'student_number' => $row['student_number'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'marked_date' => $row['marked_date'],
            'device_used' => $row['device_used'],
            'location' => $row['location']
        ];
    }
    $stmt->close();

    // Calculate attendance statistics
    $total_attendance = count($student_attendance);

    // Query to count unique students
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT student_number) AS unique_students
                            FROM attendance 
                            WHERE module_name = ? AND DATE(marked_date) = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $module_name, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    $unique_students = $stats['unique_students'];
    $stmt->close();
}

// Filter results based on search query
if ($search_query) {
    $student_attendance = array_filter($student_attendance, function($attendance) use ($search_query) {
        return strpos($attendance['student_number'], $search_query) !== false;
    });
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Attendance Dashboard</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
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
            padding: 15px 30px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: #ffffff;
            margin: 0;
            font-size: 1.5em;
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

        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            gap: 20px;
        }

        .stats-container > div {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
            min-width: 200px;
        }

        .stats-container h2 {
            margin: 0 0 10px 0;
            color: #122B40;
            font-size: 1.2em;
        }

        .stats-container p {
            font-size: 1.5em;
            margin: 0;
            color: #333;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .filter-form .form-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .filter-form input[type="date"], 
        .filter-form input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
        }

        .filter-form button {
    height: 40px; /* Set a fixed height */
    padding: 0 16px; /* Adjust padding to fit the new height */
    background: linear-gradient(120deg, #122B40, #446CB3);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 0.9em; /* Adjust font size to fit the button */
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
}

.filter-form button:hover {
    background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
}



        .table-container {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, 
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            color: #333;
        }

        table th {
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            color: #122B40;
            font-weight: 600;
        }

        table tr:hover {
            background-color: #f5f5f5;
        }

        .at-risk-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .at-risk-container h2 {
            color: #b30000;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .at-risk-table th, 
        .at-risk-table td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: #333;
        }

        .at-risk-table {
            width: 100%;
            border-collapse: collapse;
        }

        .at-risk-table th {
            background-color: #f2dede;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    
</head>
<body>
    <header>
    <a class="back-button" href="LecturerHome.php">Back</a>
        <h1>Module Attendance Dashboard</h1>
    </header>

    <div class="container">
        <!-- Filter Form -->
        <form method="post" action="" class="filter-form">
            <div class="form-group">
                <label for="attendance_date">Select Date:</label>
                <input type="date" name="attendance_date" id="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>">
            </div>
            <button type="submit">Filter</button>
            <div class="form-group">
                <label for="search_query">Search by Student Number:</label>
                <input type="text" name="search_query" id="search_query" placeholder="Enter student number..." value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" name="search">Search</button>
            <button type="submit" name="export_pdf">Export PDF</button>
        </form>

        <!-- Attendance statistics -->
        <div class="stats-container">
            <div>
                <h2>Total Attendance</h2>
                <p><?php echo $total_attendance; ?></p>
            </div>
            <div>
                <h2>Unique Students</h2>
                <p><?php echo $unique_students; ?></p>
            </div>
            <div>
                <h2>Date</h2>
                <p><?php echo htmlspecialchars($selected_date); ?></p>
            </div>
        </div>

        <!-- Attendance Records Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Student Name</th>
                        <th>Date</th>
                        <th>Device Used</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($student_attendance)): ?>
                        <?php foreach ($student_attendance as $attendance): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($attendance['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['first_name'] . ' ' . $attendance['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['marked_date']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['device_used']); ?></td>
                                <td>
                                    <?php
                                    $location = htmlspecialchars($attendance['location']);
                                    if (preg_match('/^(-?\d+\.\d+),\s*(-?\d+\.\d+)$/', $location, $matches)) {
                                        echo '<a href="#" onclick="openModal(\'' . $location . '\'); return false;">' . $location . '</a>';
                                    } else {
                                        echo $location;
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No attendance records found for the selected date.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- At-Risk Students Section -->
        <?php if (!empty($students_at_risk)): ?>
            <div class="at-risk-container">
                <h2>Students At Risk (Attendance Below <?php echo $attendance_threshold; ?>%)</h2>
                <table class="at-risk-table">
                    <thead>
                        <tr>
                            <th>Student Number</th>
                            <th>Student Name</th>
                            <th>Attendance Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_at_risk as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['attendance_percentage']); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="at-risk-container">
                <h2>No students are currently at risk.</h2>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal for Google Maps -->
    <div id="mapModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <iframe id="mapIframe" style="width: 100%; height: 500px; border: none;"></iframe>
        </div>
    </div>

    <script>
        function openModal(location) {
            var modal = document.getElementById("mapModal");
            var iframe = document.getElementById("mapIframe");
            iframe.src = "https://www.google.com/maps?q=" + location + "&output=embed";
            modal.style.display = "block";
        }

        function closeModal() {
            var modal = document.getElementById("mapModal");
            var iframe = document.getElementById("mapIframe");
            iframe.src = ""; // Clear the iframe src to stop loading the map
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById("mapModal");
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

