<?php
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

// Retrieve module code from the query parameter
$module_code = $_GET['module_code'];

// Initialize response variables
$response = [
    'student_count' => 0,
    'attendance_count' => 0,
    'average_attendance' => 0
];

// Get the total number of students enrolled in the selected module
$stmt = $conn->prepare("SELECT COUNT(DISTINCT student_number) FROM attendance WHERE module_code = ?");
$stmt->bind_param("s", $module_code);
$stmt->execute();
$stmt->bind_result($student_count);
$stmt->fetch();
$response['student_count'] = $student_count;
$stmt->close();

// Get the total attendance entries for the selected module
$stmt = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE module_code = ?");
$stmt->bind_param("s", $module_code);
$stmt->execute();
$stmt->bind_result($attendance_count);
$stmt->fetch();
$response['attendance_count'] = $attendance_count;
$stmt->close();

// Calculate the average attendance for the selected module
$total_students = 0;
$stmt = $conn->prepare("SELECT COUNT(DISTINCT student_number) FROM students");
$stmt->execute();
$stmt->bind_result($total_students);
$stmt->fetch();
$stmt->close();

if ($total_students > 0) {
    $total_days = 31; // Assume 31 days of attendance records
    $response['average_attendance'] = round(($response['attendance_count'] / ($total_students * $total_days)) * 100, 2);
}

// Close database connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
