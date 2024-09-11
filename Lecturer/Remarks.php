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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['studentNumber'])) {
    $studentNumber = $_POST['studentNumber'];
    
    if (isset($_POST['remarks'])) {
        // Handle storing remarks
        $remarks = $_POST['remarks'];
        
        $sql = "UPDATE students SET remarks = ? WHERE studentNumber = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $remarks, $studentNumber);
        
        if ($stmt->execute()) {
            echo "Remarks updated successfully.";
        } else {
            echo "Error updating remarks: " . $conn->error;
        }
        
        $stmt->close();
    } elseif (isset($_POST['deleteRemarks'])) {
        // Handle deleting remarks
        $sql = "UPDATE students SET remarks = NULL WHERE studentNumber = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $studentNumber);
        
        if ($stmt->execute()) {
            echo "Remarks deleted successfully.";
        } else {
            echo "Error deleting remarks: " . $conn->error;
        }
        
        $stmt->close();
    }
    
    $conn->close();
    exit();
}

// Fetch students from the database
$sql = "SELECT studentNumber, email, first_name, last_name, cellphone, gender, course, year_of_study, remarks FROM students";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Remarks</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .remarks-button, .delete-remarks-button {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            display: block;
            margin-top: 5px;
        }
        .remarks-button:hover, .delete-remarks-button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
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
</head>
<body>
    <header>
        &nbsp;<h1>Students</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <div class="container">
        <h2>List of Registered Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Cellphone</th>
                    <th>Gender</th>
                    <th>Course</th>
                    <th>Year of Study</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['studentNumber']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['cellphone']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['course']); ?></td>
                            <td><?php echo htmlspecialchars($row['year_of_study']); ?></td>
                            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                            <td>
                                <button class="remarks-button" onclick="addRemarks('<?php echo htmlspecialchars($row['studentNumber']); ?>')">Add</button>
                                <button class="delete-remarks-button" onclick="deleteRemarks('<?php echo htmlspecialchars($row['studentNumber']); ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function addRemarks(studentNumber) {
            let remarks = prompt("Enter remarks for student " + studentNumber + ":");
            let module = prompt("Enter the module for the remarks:");
            if (remarks !== null && remarks !== "" && module !== null && module !== "") {
                // Send the remarks to the server via AJAX
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert(xhr.responseText);
                        location.reload();
                    }
                };
                xhr.send("studentNumber=" + encodeURIComponent(studentNumber) + "&remarks=" + encodeURIComponent(remarks) + "&module=" + encodeURIComponent(module));
            }
        }
        
        function deleteRemarks(studentNumber) {
            let module = prompt("Enter the module for which to delete the remarks:");
            if (module !== null && module !== "") {
                if (confirm("Are you sure you want to delete the remarks for student " + studentNumber + " in module " + module + "?")) {
                    // Send the request to delete the remarks via AJAX
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert(xhr.responseText);
                            location.reload();
                        }
                    };
                    xhr.send("studentNumber=" + encodeURIComponent(studentNumber) + "&deleteRemarks=true&module=" + encodeURIComponent(module));
                }
            }
        }
    </script>
</body>
</html>