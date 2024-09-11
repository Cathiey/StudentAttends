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

$message = "";

// Function to generate a default password
function generateDefaultPassword($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submitType = $_POST['submit'];

    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $gender = $conn->real_escape_string($_POST['gender']);
    
    // Generate default password
    $defaultPassword = generateDefaultPassword();
    $password = password_hash($defaultPassword, PASSWORD_BCRYPT);

    $exists = false;
    
    if ($submitType == "student") {
        $studentNumber = $conn->real_escape_string($_POST['student_number']);
        
        // Check if student number already exists
        $sql_check = "SELECT * FROM students WHERE studentNumber = '$studentNumber'";
        $result_check = $conn->query($sql_check);
        
        if ($result_check->num_rows > 0) {
            $exists = true;
            $message = "Student number already exists.";
        } else {
            // Insert new student
            $course = $conn->real_escape_string($_POST['course']);
            $yearOfStudy = $_POST['year_of_study'];

            $sql = "INSERT INTO students (studentNumber, email, first_name, last_name, cellphone, gender, course, year_of_study, password) 
                    VALUES ('$studentNumber', '$email', '$firstName', '$lastName', '$phone', '$gender', '$course', '$yearOfStudy', '$password')";

            if ($conn->query($sql) === TRUE) {
                $message = "New student added successfully! Default password is: $defaultPassword";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } elseif ($submitType == "lecturer") {
        $staffNo = $conn->real_escape_string($_POST['staff_no']);
        
        // Check if staff number already exists
        $sql_check = "SELECT * FROM lecturers WHERE staffNo = '$staffNo'";
        $result_check = $conn->query($sql_check);
        
        if ($result_check->num_rows > 0) {
            $exists = true;
            $message = "Staff number already exists.";
        } else {
            // Insert new lecturer
            $department = $conn->real_escape_string($_POST['department']);
            $position = $conn->real_escape_string($_POST['position']);

            $sql = "INSERT INTO lecturers (staffNo, email, first_name, last_name, phone, gender, department, position, password) 
                    VALUES ('$staffNo', '$email', '$firstName', '$lastName', '$phone', '$gender', '$department', '$position', '$password')";

            if ($conn->query($sql) === TRUE) {
                $message = "New lecturer added successfully! Default password is: $defaultPassword";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    // Pass the message and alert type to JavaScript
    $alertType = $exists ? "error" : "success";
    echo "<script>
            window.onload = function() {
                var message = '" . addslashes($message) . "';
                var alertType = '" . $alertType . "';
                if (alertType === 'success') {
                    alert(message);
                } else {
                    alert(message);
                }
            }
          </script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
       @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500&display=swap");
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            color: #333;
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
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between; 
            text-align: center; 
        }
        header h1 {
            margin: 0;
            font-size: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
        .container {
    width: 600px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f0f2f5;;
    margin: auto;
    margin-top: 20px;
    display: flex;
    flex-direction: column; /* Stack elements vertically */
    align-items: center; /* Center the contents */
    margin-bottom: 20px;
}

        .form-container {
            width: 48%;
            display: none; /* Hide all forms initially */
        }
        .form-container.active {
            display: block; /* Show the active form */
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container form label {
            margin-bottom: 5px;
        }
        .form-container form input,
        .form-container form select,
        .form-container form button {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container form button {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container form button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
        .message {
            text-align: center;
            color: green;
        }
        .tabs {
            text-align: center;
            margin-bottom: 20px;
        }
        .tabs button {
            padding: 10px 20px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .tabs button.active {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transform: scale(1.05);
            color: #122B40;
        }
        .tabs button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
           
        }
        .logo-container {
    margin-top: 20px;
    text-align: center;
}

.logo-container img {
    max-width: 200px;
    height: auto;
}

    </style>
    <script>
        function toggleForm(userType) {
            var studentForm = document.getElementById("student_form");
            var lecturerForm = document.getElementById("lecturer_form");
            var studentButton = document.getElementById("student_button");
            var lecturerButton = document.getElementById("lecturer_button");
            
            if (userType === "student") {
                studentForm.classList.add("active");
                lecturerForm.classList.remove("active");
                studentButton.classList.add("active");
                lecturerButton.classList.remove("active");
            } else if (userType === "lecturer") {
                studentForm.classList.remove("active");
                lecturerForm.classList.add("active");
                studentButton.classList.remove("active");
                lecturerButton.classList.add("active");
            }
        }

        window.onload = function() {
            // Show student form by default
            toggleForm("student");
        };
    </script>
</head>
<body>
<header>
    &nbsp;<h1>Add User</h1>
    <a class="back-button" onclick="history.back()">Back</a>
</header>
<main>
    <div class="container">
        <div class="tabs">
            <button id="student_button" onclick="toggleForm('student')">Add Student</button>
            <button id="lecturer_button" onclick="toggleForm('lecturer')">Add Lecturer</button>
        </div>
        
        <div id="student_form" class="form-container">
       
            <h2>Add Student</h2>
            <form method="POST" action="">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
                
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone">
                
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo isset($defaultPassword) ? $defaultPassword : ''; ?>" readonly>
                
                <label for="student_number">Student Number:</label>
                <input type="text" id="student_number" name="student_number">
                
                <label for="course">Course:</label>
                <select name="course" required>
                    <option value="">Select Course</option>
                    <option value="Higher Certificate in ICT in User Support">Higher Certificate in ICT in User Support</option>
                    <option value="Diploma in ICT Application development">Diploma in ICT Application development</option>
                    <option value="Bachelor in ICT">Bachelor in ICT</option>
                </select>
                
                <label for="year_of_study">Year of Study:</label>
                <select name="year_of_study" required>
                    <option value="">Select Year</option>
                    <option value="1s">First Year</option>
                    <option value="2nd">Second Year</option>
                    <option value="3rd">Third Year</option>
                    <option value="4th">Fourth Year</option>
                </select>
                
                <button type="submit" name="submit" value="student">Add Student</button>
            </form>
        </div>
        <div id="lecturer_form" class="form-container">
            <h2>Add Lecturer</h2>
            <form method="POST" action="">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
                
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone">
                
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo isset($defaultPassword) ? $defaultPassword : ''; ?>" readonly>
                
                <label for="staff_no">Staff Number:</label>
                <input type="text" id="staff_no" name="staff_no">
                
                <label for="department">Department:</label>
                <input type="text" id="department" name="department">
                
                <label for="position">Position:</label>
                <input type="text" id="position" name="position">
                
                <button type="submit" name="submit" value="lecturer">Add Lecturer</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
