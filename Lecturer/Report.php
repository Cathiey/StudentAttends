<?php
// Start the session to use session variables
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

// Fetch modules from the database
$sql = "SELECT DISTINCT module_name FROM Attendance";
$modulesResult = $conn->query($sql);

$attendanceData = [];
$totalDays = 0;
$studentNumber = '';
$studentName = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['generate-report'])) {
        // Handle report generation
        $moduleName = $conn->real_escape_string($_POST['class']);
        $studentNumber = $conn->real_escape_string($_POST['student']);
        $startDate = $conn->real_escape_string($_POST['start_date']);
        $endDate = $conn->real_escape_string($_POST['end_date']);

        if ($studentNumber && $startDate && $endDate) {
            // Fetch student name from the students table
            $studentSql = "SELECT first_name, last_name FROM students WHERE studentNumber = '$studentNumber'";
            $studentResult = $conn->query($studentSql);
            if ($studentResult && $studentResult->num_rows > 0) {
                $studentRow = $studentResult->fetch_assoc();
                $studentName = $studentRow['first_name'] . ' ' . $studentRow['last_name'];
            } else {
                $studentName = "Unknown Student";
            }

            if ($moduleName == "all") {
                // Fetch attendance data for all modules that the student attended
                $attendanceSql = "SELECT marked_date, module_name, device_used, location 
                                  FROM Attendance
                                  WHERE student_number = '$studentNumber'
                                  AND marked_date BETWEEN '$startDate' AND '$endDate'";
            } else {
                // Fetch attendance data for the selected module
                $attendanceSql = "SELECT marked_date, module_name, device_used, location 
                                  FROM Attendance
                                  WHERE module_name = '$moduleName' 
                                  AND student_number = '$studentNumber'
                                  AND marked_date BETWEEN '$startDate' AND '$endDate'";
            }

            $attendanceResult = $conn->query($attendanceSql);

            if ($attendanceResult) {
                if ($attendanceResult->num_rows > 0) {
                    while ($row = $attendanceResult->fetch_assoc()) {
                        $attendanceData[] = $row;
                        $totalDays++;
                    }
                } else {
                    echo "<script>console.log('No results found.');</script>";
                }
            } else {
                echo "<script>console.log('Error in query: " . $conn->error . "');</script>";
            }

            // Store the attendance data in session for PDF export
            $_SESSION['attendanceData'] = $attendanceData;
            $_SESSION['studentName'] = $studentName;
            $_SESSION['studentNumber'] = $studentNumber;
            $_SESSION['totalDays'] = $totalDays;
        } else {
            echo "<script>console.log('Invalid input.');</script>";
        }
    } elseif (isset($_POST['export-pdf'])) {
        // Handle PDF export

        // Retrieve attendance data from the session
        $attendanceData = $_SESSION['attendanceData'];
        $studentName = $_SESSION['studentName'];
        $studentNumber = $_SESSION['studentNumber'];
        $totalDays = $_SESSION['totalDays'];

        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Student Attends');
        $pdf->SetTitle('Attendance Report');
        $pdf->SetSubject('Attendance Report');
        $pdf->SetKeywords('TCPDF, PDF, attendance, report');

        // Set header data with logo
        $logoPath = 'C:/xampp/htdocs/StudentAttends/Lecturer/ump.jpeg'; // Update the path if necessary
        $pdf->SetHeaderData($logoPath, 0, 'Student Attends', 'Attendance Report', array(0,64,255), array(0,64,128));

        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Add a page
        $pdf->AddPage();
        $pdf->Image($logoPath, 160, 25, 25, '', 'JPEG', '', 'T', false, 250, '', false, false, 0, false, false, false);

        // Set font
        $pdf->SetFont('helvetica', 'B', 12);

        // Title
        $pdf->Write(0, 'University of Mpumalanga', '', 0, 'L', true, 0, false, false, 0);
        $pdf->Ln(4); // Line break

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Student Information
        $pdf->Write(0, 'Student Name: ' . $studentName, '', 0, 'L', true, 0, false, false, 0);
        $pdf->Write(0, 'Student Number: ' . $studentNumber, '', 0, 'L', true, 0, false, false, 0);
        $pdf->Ln(4); // Line break

       // Table Header
$html = '
<table border="1" cellpadding="4">
    <thead>
        <tr style="background-color: #d3e6f9; font-weight: bold;">
            <th>Date</th>
            <th>Module Name</th>
            <th>Device Used</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>';

// Table Body
foreach ($attendanceData as $attendance) {
    $html .= '<tr>
        <td>' . htmlspecialchars($attendance['marked_date']) . '</td>
        <td>' . htmlspecialchars($attendance['module_name']) . '</td>
        <td>' . htmlspecialchars($attendance['device_used']) . '</td>
        <td>' . htmlspecialchars($attendance['location']) . '</td>
    </tr>';
}

$html .= '</tbody></table>';


        // Print text using writeHTML method
        $pdf->writeHTML($html, true, false, true, false, '');

        // Summary
        $pdf->Ln(4); // Line break
        $pdf->Write(0, 'Total Days: ' . $totalDays, '', 0, 'L', true, 0, false, false, 0);

        // Close and output PDF document
        $pdf->Output('Attendance_Report.pdf', 'D');
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance Report</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        main {
            padding: 100px 20px 20px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        

        nav { background: #f0f2f5; overflow: hidden; }
        nav a { float: left; display: block; color: #122B40; text-align: center; padding: 14px 16px; text-decoration: none; }
        nav a:hover { background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF); border-radius: 5px; }
        .container { padding: 20px; }
        .filters { margin-bottom: 20px; }
        .filters label { margin-right: 10px; }
       
        .report-section {
    margin-top: 20px;
    padding: 20px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);

}

.report-header {
    border-bottom: 2px solid #003366;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.report-header h2 {
    font-size: 22px;
    font-weight: bold;
    color: #003366;
    margin: 0;
}

.report-header p {
    font-size: 16px;
    margin: 5px 0;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.report-table thead {
    background-color: #003366;
    color: white;
}

.report-table th, .report-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.report-table th {
    font-weight: bold;
}

.report-table td {
    background-color: #f9f9f9;
}

.report-table tr:nth-child(even) td {
    background-color: #f1f1f1;
}

.report-table tr:hover td {
    background-color: #e0e0e0;
}

.summary {
    font-size: 18px;
    font-weight: bold;
    color: #003366;
}

.summary p {
    margin: 0;
}

        footer {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(120deg, #122B40, #446CB3);
            margin-top: auto;
        }
        footer a {
            color: #122B40;
            text-decoration: none;
        }
        </style>
</head>
<body>
    <header>
        <h1>Attendance Report</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <main>
        <div class="container">
            <!-- Selection Section -->
            <form method="POST" action="">
                <div class="filters">
                    <label for="start-date">Start Date:</label>
                    <input type="date" id="start-date" name="start_date" required>
                    <label for="end-date">End Date:</label>
                    <input type="date" id="end-date" name="end_date" required>
                    <label for="class">Class/Grade:</label>
                    <select id="class" name="class" required>
                        <option value="all">All Classes</option>
                        <?php if ($modulesResult->num_rows > 0): ?>
                            <?php while($module = $modulesResult->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($module['module_name']); ?>">
                                    <?php echo htmlspecialchars($module['module_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <label for="student">Student:</label>
                    <input type="text" id="student" name="student" placeholder="Enter Student Number" required>
                    <button type="submit" name="generate-report" id="generate-report">Generate Report</button>
                </div>
            </form>

           <!-- Report Section -->
<?php if (!empty($attendanceData)): ?>
<div class="report-section">
    <div class="report-header">
        <h2>Attendance Report</h2>
        <p><strong>Student Name:</strong> <?php echo htmlspecialchars($studentName); ?></p>
        <p><strong>Student Number:</strong> <?php echo htmlspecialchars($studentNumber); ?></p>
    </div>
    <table class="report-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Module Name</th>
                <th>Device Used</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceData as $attendance): ?>
                <tr>
                    <td><?php echo htmlspecialchars($attendance['marked_date']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['module_name']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['device_used']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['location']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
               <!-- Export PDF Button -->
               <form method="POST" action="">
                   <button type="submit" name="export-pdf" id="export-pdf">Export as PDF</button>
               </form>
           </div>
       <?php endif; ?>
   </main>
</body>
</html>
