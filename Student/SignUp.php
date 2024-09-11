<?php

// Check if form is submitted
if (isset($_POST['submit'])) {
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

    // Set parameters
    $studentNumber = $_POST['studentNumber'];
    $email = $_POST['email'];
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $cellphone = $_POST['cellphone'];
    $gender = $_POST['gender'];
    $course = $_POST['course'];
    $year_of_study = $_POST['yearOfStudy'];
    $password = $_POST['password'];

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if student number or email already exists
    $check_query = "SELECT * FROM students WHERE studentNumber = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $studentNumber, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('An account with the provided student number or email already exists.');</script>";
    } else {
        // Prepare and bind SQL statement
        $stmt = $conn->prepare("INSERT INTO students (studentNumber, email, first_name, last_name, cellphone, gender, course, year_of_study, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $studentNumber, $email, $first_name, $last_name, $cellphone, $gender, $course, $year_of_study, $hashed_password);

        // Execute SQL statement
        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully');</script>";
            header("Location: studentlogin.php"); // Redirect to login page
            exit();
        } else {
            echo "<script>alert('Error creating account');</script>";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            animation: slide-in 0.7s ease-out;
        }
        @keyframes slide-in {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            animation: bounce-in 1.5s ease-in-out;
        }
        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .floating-shapes div {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(120deg, #122B40, #446CB3);
            animation: float 6s ease-in-out infinite;
            opacity: 0.8;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        .shape1 { width: 80px; height: 80px; top: 5%; left: 18%; animation-duration: 8s; }
        .shape2 { width: 90px; height: 90px; top: 35%; left: 2%; animation-duration: 10s; }
        .shape3 { width: 100px; height: 100px; top: 65%; left: 15%; animation-duration: 12s; }
        .shape4 { width: 80px; height: 80px; top: 5%; left: 76%; animation-duration: 8s; }
        .shape5 { width: 90px; height: 90px; top: 35%; left: 91%; animation-duration: 10s; }
        .shape6 { width: 100px; height: 100px; top: 65%; left: 77%; animation-duration: 12s; }
        .shape7 { width: 70px; height: 70px; top: 85%; left: 5%; animation-duration: 8s; }
        .shape8 { width: 70px; height: 70px; top: 85%; left: 90%; animation-duration: 8s; }
        .logo {
            position: absolute;
            top: 15px;
            right: 50px;
            max-width: 110px;
            height: auto;
            margin: 0;
        }
        .wrapper {
            position: relative;
            max-width: 600px;
            width: 100%;
            background: #fff;
            padding: 34px;
            border-radius: 6px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
            animation: bounce-in 1s ease-in-out;
        }
        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .wrapper h2 {
            position: relative;
            font-size: 22px;
            font-weight: 600;
            color: #122B40;
        }
        .wrapper h2::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 28px;
            border-radius: 12px;
            background: #122B40;
        }
        .wrapper form .input-box {
            height: 52px;
            margin: 18px 0;
        }
        form .input-box input {
            height: 100%;
            width: 100%;
            outline: none;
            padding: 0 15px;
            font-size: 17px;
            font-weight: 400;
            color: #333;
            border: 1.5px solid #C7BEBE;
            border-bottom-width: 2.5px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .input-box input:focus,
        .input-box input:valid {
            border-color: #90B2D8;
        }
        form .policy {
            display: flex;
            align-items: center;
        }
        form h3 {
            font-size: 14px;
            font-weight: 500;
            margin-left: 10px;
        }
        .input-box.button input {
            color: #fff;
            letter-spacing: 1px;
            border: none;
            background: linear-gradient(120deg, #122B40, #446CB3);
            cursor: pointer;
        }
        .input-box.button input:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
        form .text h3 {
            color: #333;
            width: 100%;
            text-align: center;
        }
        form .text h3 a {
            color: #122B40;
            text-decoration: none;
        }
        form .text h3 a:hover {
            text-decoration: underline;
        }
        .input-inline {
            display: flex;
            gap: 10px;
        }
        .wrapper {
            position: relative;
            max-width: 600px;
            width: 100%;
            background: #fff;
            padding: 34px;
            border-radius: 6px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape1"></div>
        <div class="shape2"></div>
        <div class="shape3"></div>
        <div class="shape4"></div>
        <div class="shape5"></div>
        <div class="shape6"></div>
        <div class="shape7"></div>
        <div class="shape8"></div>
    </div>
    <div class="wrapper">
        <img src="LogoUMP.png" alt="Logo" class="logo">
        <h2>CREATE ACCOUNT</h2>
        <form action="SignUp.php" method="POST" class="registration-form">
            <div class="input-inline">
                <div class="input-box">
                    <input type="text" name="studentNumber" placeholder="Student Number" required>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
            </div>
            <div class="input-inline">
                <div class="input-box">
                    <input type="text" name="firstName" placeholder="First Name" required>
                </div>
                <div class="input-box">
                    <input type="text" name="lastName" placeholder="Last Name" required>
                </div>
            </div>
            <div class="input-inline">
                <div class="input-box">
                    <input type="tel" name="cellphone" placeholder="Cellphone" required>
                </div>
                <div class="input-box">
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="input-inline">
                <div class="input-box">
                    <select name="course" required>
                        <option value="">Select Course</option>
                        <option value="Higher Certificate in ICT in User Support">Higher Certificate in ICT in User Support</option>
                        <option value="Diploma in ICT Application development">Diploma in ICT Application Development</option>
                        <option value="Bachelor in ICT">Bachelor in ICT</option>
                    </select>
                </div>
                <div class="input-box">
                    <select name="yearOfStudy" required>
                        <option value="">Select Year</option>
                        <option value="1st">First Year</option>
                        <option value="2nd">Second Year</option>
                        <option value="3rd">Third Year</option>
                        <option value="4th">Fourth Year</option>
                    </select>
                </div>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Create Password" required>
            </div>
            <div class="policy">
                <input type="checkbox" required>
                <h3>I accept all terms & conditions</h3>
            </div>
            <div class="input-box button">
                <input type="submit" name="submit" value="Sign Up">
            </div>
            <div class="text">
                <h3><a href="StudentLogin.php">Already have an account? Login now</a></h3>
            </div>
        </form>
    </div>
</body>
</html>
