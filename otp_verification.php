<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studentattends_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$otp = '';
$email = '';
$error = '';
$success = '';

// Retrieve email from URL query parameter
if (isset($_GET['email'])) {
    $email = trim($_GET['email']);
} else {
    $error = "Email address is missing.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);

    // Check if OTP matches the one in the database for that email
    $stmt = $conn->prepare("SELECT otp, otp_expires_at FROM students WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $db_otp = $row['otp'];
            $otp_expires_at = $row['otp_expires_at'];

            // Check if OTP matches and is not expired
            if ($otp === $db_otp && strtotime($otp_expires_at) > time()) {
                $success = "OTP verified successfully. You can now reset your password.";
                // Redirect to reset password page or display password reset form here
                header("Location: reset_password.php?email=" . urlencode($email));
                exit;
            } else {
                $error = "Invalid or expired OTP.";
            }
        } else {
            $error = "No account found with that email.";
        }
    } else {
        $error = "Database query error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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

        .otp-verification-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 20px;
            box-sizing: border-box;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        button {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        input[type="text"] {
            width: 95%;
        }

        button {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }

        .message {
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="otp-verification-container">
        <h2>Verify OTP</h2>
        <p>Email succesfully sent!<br>Please enter the OTP that was sent to your email address.</p>
        <form method="POST" action="">
            <input type="text" name="otp" placeholder="Enter the OTP" required>
            <button type="submit">Verify OTP</button>
        </form>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        <p>Don't have an OTP? <a href="ForgotPassword.php">Request OTP here</a></p>
    </div>
</body>
</html>
