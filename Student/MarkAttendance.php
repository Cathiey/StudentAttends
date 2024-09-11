<?php
session_start();

// Display errors for debugging (remove or disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    http_response_code(403);
    echo json_encode(array('status' => 'error', 'message' => 'Not logged in.'));
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studentattends_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array('status' => 'error', 'message' => 'Database connection failed.'));
    exit();
}

// Get data from POST request
$studentNumber = isset($_POST['student_number']) ? trim($_POST['student_number']) : '';
$moduleName = isset($_POST['module_name']) ? trim($_POST['module_name']) : '';
$deviceUsed = isset($_POST['device_used']) ? trim($_POST['device_used']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$markedDate = date('Y-m-d H:i:s'); // Current date and time

// Validate input
if (empty($studentNumber) || empty($moduleName)) {
    http_response_code(400);
    echo json_encode(array('status' => 'error', 'message' => 'Student number and module name are required.'));
    exit();
}

// Prepare SQL query to insert attendance
$sql = "INSERT INTO attendance (student_number, module_name, device_used, location, marked_date) 
VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(array('status' => 'error', 'message' => 'Error preparing SQL statement: ' . $conn->error));
    exit();
}

$stmt->bind_param("sssss", $studentNumber, $moduleName, $deviceUsed, $location, $markedDate);

// Execute the statement and provide appropriate responses
$response = array();
if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Attendance recorded successfully';
} else {
    http_response_code(500);
    $response['status'] = 'error';
    $response['message'] = 'Error executing SQL statement: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
