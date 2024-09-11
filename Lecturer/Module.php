<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: Stafflogin.php");
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

// Fetch lecturer's staff number
$staffNo = $_SESSION['staffNo'];

// Prepare and execute query to get modules the lecturer is teaching
$sql = "SELECT m.Module_Name
        FROM Modules m
        JOIN lecturer_modules lm ON m.module_id = lm.module_id
        JOIN lecturers l ON lm.lecturer_id = l.id
        WHERE l.staffNo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staffNo);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
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
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            margin: 0;
            font-size: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            margin-top: 80px;
        }

        h2 {
            font-size: 26px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .module-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #ddd;
        }

        .module-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .module-card a {
            text-decoration: none;
            color: #122B40;
            font-size: 20px;
            
            display: block;
        }

        .module-card a:hover {
            color: #446CB3;
        }

        
    </style>
</head>
<body>
    <header>
    &nbsp; <h1>Modules</h1>
    <a class="back-button" href="LecturerHome.php">Back</a>
    </header>
    <div class="container">
        <h2>Modules You Lecture and Moderate</h2>
        <div class="module-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="module-card">
                        <a href="Attendance.php?module_name=<?php echo urlencode($row['Module_Name']); ?>"><?php echo htmlspecialchars($row['Module_Name']); ?></a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="module-card">
                    <p>No modules found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
   
</body>
</html>
