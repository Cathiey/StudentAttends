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

// Get student enrolled modules
$studentNumber = $_SESSION['studentNumber'];
$stmt = $conn->prepare("SELECT m.module_name FROM modules m JOIN student_modules sm ON m.module_id = sm.module_id WHERE sm.student_number = ?");
$stmt->bind_param("s", $studentNumber);
$stmt->execute();
$moduleResult = $stmt->get_result();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['letter']) && isset($_POST['module'])) {
    $module = $_POST['module'];
    $file = $_FILES['letter'];
    
    // Validate file
    if ($file['error'] == UPLOAD_ERR_OK) {
        $fileTmpName = $file['tmp_name'];
        $fileName = basename($file['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Define allowed file extensions and directory
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $uploadDir = 'uploads/';
        $uploadFilePath = $uploadDir . $fileName;
        
        if (in_array($fileExtension, $allowedExtensions)) {
            if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                // Save file info to database
                $stmt = $conn->prepare("INSERT INTO uploads (student_number, module_name, file_path, upload_date) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $studentNumber, $module, $uploadFilePath);
                $stmt->execute();
                $stmt->close();
                
                echo "<script>alert('File uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error uploading file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only PDF, DOC, and DOCX files are allowed.');</script>";
        }
    } else {
        echo "<script>alert('Error with file upload.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Letter</title>
    <link rel="stylesheet" href="styles.css">
    <style>
          html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            animation: pageFlipIn 1s ease-out;
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
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: white;
            padding: 20px;
        }
        h1 {
            color: #122B40;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        select, input[type="file"], button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            color: #122B40;
            transform: scale(1.05);
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        footer {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
           
        }
        footer a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
<header>
    &nbsp;<h1>Absence Letter Upload</h1>
    <a class="back-button" href="StudentHome.php">Back</a>
    </header>

    <div class="container">
    <div class="form-container">
        <h1>Upload Your Absence Letter</h1>
        <form action="Uploads.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="module">Select Module</label>
                <select name="module" id="module" required>
                    <option value="">Select Module</option>
                    <?php
                    if ($moduleResult->num_rows > 0) {
                        while ($row = $moduleResult->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['module_name']) . '">' . htmlspecialchars($row['module_name']) . '</option>';
                        }
                    } else {
                        echo '<option value="">No modules available</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="letter">Upload Letter</label>
                <input type="file" name="letter" id="letter" accept=".pdf, .doc, .docx" required>
            </div>
            <button type="submit">Upload Letter</button>
        </form>
        </div>
        </div>

</body>
</html>
