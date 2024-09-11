<?php
// Load Composer's autoloader for PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studentattends_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Fetch user's first name from the database
        $stmt = $conn->prepare("SELECT first_name FROM lecturers WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($first_name);
            $stmt->fetch();
            $stmt->close();
        }

        // Check if the email exists
        if (empty($first_name)) {
            $error = "No account found with that email address.";
        } else {
            // Generate a random OTP
            $otp = rand(100000, 999999);
            // Extend OTP expiration time to 2 minutes
            $otp_expires_at = date("Y-m-d H:i:s", strtotime('+2 minutes'));

            // Store OTP and expiration in the database
            $stmt = $conn->prepare("UPDATE lecturers SET otp = ?, otp_expires_at = ? WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("sss", $otp, $otp_expires_at, $email);
                $stmt->execute();
                
                // Send OTP email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'botauto212@gmail.com';
                    $mail->Password = 'cjeifgsiqfivevdx'; // Replace with your email password or app-specific password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('botauto212@gmail.com', 'STUDENT ATTENDS');
                    $mail->addAddress($email);
                    $mail->Subject = 'Password Reset Request';
                    // HTML content
                    $mail->isHTML(true);
                    $mail->Body = "
                        <html>
                        <head>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    background-color: #f4f4f4;
                                    margin: 0;
                                    padding: 20px;
                                }
                                .container {
                                    background-color: #ffffff;
                                    border-radius: 8px;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                                    max-width: 600px;
                                    margin: auto;
                                    padding: 20px;
                                }
                                h2 {
                                    color: #333;
                                }
                                p {
                                    color: #555;
                                    font-size: 16px;
                                    line-height: 1.5;
                                }
                                .otp {
                                    background-color: #e0f7fa;
                                    border-left: 4px solid #00796b;
                                    padding: 10px;
                                    margin: 20px 0;
                                    font-size: 18px;
                                    font-weight: bold;
                                }
                                .footer {
                                    margin-top: 20px;
                                    font-size: 14px;
                                    color: #888;
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
                            <div class='container'>
                                <h2>Password Reset Request</h2>
                                <p>Dear $first_name,</p>
                                <p>We received a request to reset your password. Please use the following One-Time Password (OTP) to reset your password:</p>
                                <div class='otp'>$otp</div>
                                <p>This OTP is valid for the next 2 minutes. If you did not request this, please ignore this email.</p>
                                <p>Thank you!</p>
                                <div class='footer'>
                                    <p>If you have any questions, feel free to contact us at <a href='mailto:support@studentattends.com'>support@studentattends.com</a>.</p>
                                </div>
                            </div>
                        </body>
                        </html>
                    ";
                    
                    $mail->send();
                    
                    // Redirect to OTP verification page with email as query parameter
                    header("Location: AFVerify_OTP.php?email=" . urlencode($email));
                    exit;
                } catch (Exception $e) {
                    $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Database query error.";
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
    <title>Request OTP</title>
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

        .otp-verification-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 30px;
            box-sizing: border-box;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        p {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
            font-size: 16px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="email"],
        button {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 12px;
            font-size: 16px;
        }

        input[type="email"] {
            width: 95%;
        }

        button {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s;
        }

        button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }

        .message {
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            margin-top: 15px;
            font-size: 14px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
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
        <h2>Forgot Password</h2>
        <p>Enter your email address to receive an OTP for password reset. Check your email for further instructions.</p>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send OTP</button>
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
