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

// Handle staff deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // Handle deletion
    $idsToDelete = $_POST['delete'];
    
    foreach ($idsToDelete as $staffNo) {
        // Get staff data
        $sqlSelect = "SELECT * FROM lecturers WHERE staffNo = ?";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->bind_param("s", $staffNo);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        $staffData = $result->fetch_assoc();
        
        if ($staffData) {
            // Insert into deleted_staff table
            $sqlInsert = "INSERT INTO deleted_staff (staffNo, first_name, last_name, email, phone, gender, department, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssssssss", $staffData['staffNo'], $staffData['first_name'], $staffData['last_name'], $staffData['email'], $staffData['phone'], $staffData['gender'], $staffData['department'], $staffData['position']);
            $stmtInsert->execute();
            
            // Delete from staff table
            $sqlDelete = "DELETE FROM lectures WHERE staffNo = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param("s", $staffNo);
            $stmtDelete->execute();
        }
    }
    // Set deleted message for alert
    $deletedMessage = count($idsToDelete) > 1 ? "Staff members successfully deleted." : "Staff member successfully deleted.";
}

// Fetch staff members from the database
$sql = "SELECT staffNo, first_name, last_name, email, phone, gender, department, position FROM lecturers";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Staff</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            animation: pageFlipIn 0.7s ease-out;
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

.page-flip {
    transform-style: preserve-3d;
    animation: pageFlipIn 1s ease-in-out;
}

        header {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between; 
            text-align: center; /* Center align text within header */
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
        .delete-button {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .delete-button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-align: left;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        h2 {
            font-size: 24px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif; /* Different font family */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Add a shadow */
        }
        @media (max-width: 768px) {
            .header a {
                float: none;
                display: block;
                text-align: center;
                margin: 10px 0;
            }
            th, td {
                padding: 8px;
            }
        }
        @media (max-width: 576px) {
            .container {
                padding: 10px;
            }
            th, td {
                padding: 5px;
                font-size: 14px;
            }
        }
       
        
    </style>
    <script>
        // JavaScript to display alert message after deletion
        <?php if (isset($deletedMessage)): ?>
            window.onload = function() {
                alert("<?php echo $deletedMessage; ?>");
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <header>
    &nbsp;<h1>Staff</h1>
    <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <div class="container">
        <h2>List of Registered Staff</h2>
        <form method="POST" action="">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Staff Number</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Cellphone</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="delete[]" value="<?php echo htmlspecialchars($row['staffNo']); ?>"></td>
                                <td><?php echo htmlspecialchars($row['staffNo']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No staff found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="submit" class="delete-button">Archive Staff</button>
        </form>
    </div>
   
</body>
</html>