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
$password = '';
$confirm_password = '';
$email = '';
$error = '';
$success = '';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);

    // Validate passwords
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Update the password in the database
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password
        $stmt = $conn->prepare("UPDATE students SET password = ? WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $hashed_password, $email);
            if ($stmt->execute()) {
                $success = "Password reset successfully.";
                
                // Redirect to login page after successful password reset
                header("Location: http://localhost/StudentAttends/Student/StudentLogin.php");
                exit;
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        .reset-password-container {
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

        input[type="password"],
        button {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        input[type="password"] {
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
    </style>
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <form method="POST" action="">
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly hidden>
            <input type="password" name="password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
