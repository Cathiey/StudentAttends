
<?php
session_start();

// Check if lecturer is logged in
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['staffNo'])) {
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

// Fetch lecturer ID based on session staffNo
$staffNo = $_SESSION['staffNo'];
$sql = "SELECT id FROM lecturers WHERE staffNo = '$staffNo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lecturerId = $row['id'];
} else {
    die("Lecturer not found.");
}

// Fetch modules available for the lecturer to select
$sql = "SELECT module_id, module_name, module_code FROM modules";
$result = $conn->query($sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modules'])) {
    $modules = $_POST['modules'];

    // Insert each selected module for the lecturer
    foreach ($modules as $module_id) {
        $insertSQL = "INSERT INTO lecturer_modules (lecturer_id, module_id) VALUES ('$lecturerId', '$module_id')";
        if ($conn->query($insertSQL) === TRUE) {
            // Successfully inserted
        } else {
            // Handle any errors if needed
            echo "<script>alert('Error occurred while saving modules: " . $conn->error . "');</script>";
        }
    }

    // After inserting, redirect or notify the user
    echo "<script>
        alert('Modules saved successfully');
        window.location.href = 'LecturerHome.php';
    </script>";
    exit();
}

// Handle AJAX request for module search
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);
    $sql = "SELECT module_id, module_name, module_code FROM modules WHERE module_name LIKE '%$query%'";
    $result = $conn->query($sql);

    $modules = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $modules[] = $row;
        }
    }
    echo json_encode($modules);
    exit();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Module Selection</title>
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
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0056b3;
            padding: 10px 20px;
        }
        main {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .container {
            max-width: 1000px;
            width: 700px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: white;
        }
        h2 {
            font-size: 24px;
            color: #122B40;
            margin-bottom: 20px;
            font-family: 'Arial Black', sans-serif; /* Different font family */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Add a shadow */
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        .module-item {
            margin-bottom: 10px;
        }
        label {
            display: inline-block;
            vertical-align: middle;
            margin-left: 10px;
        }
        input[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
        .search-bar {
            margin-top: 10px;
            display: flex;
            justify-content: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            outline: none;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        .search-bar button:hover {
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
        #search-results {
            margin-top: 20px;
        }
       
        </style>
</head>
<body>
    <header>
    &nbsp;<h1>Modules Lecturerd</h1>
    <a class="back-button" href="LecturerHome.php">Back</a>
    </header>
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search modules...">
        <button onclick="searchModules()">Search</button>
    </div>
    <main>
        <div class="container">
            <div id="search-results"></div>
            <h2>Select Modules you Teach & Moderate</h2>
            <form action="Modules.php" method="post">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="module-item">';
                        echo '<input type="checkbox" id="module_' . $row['module_id'] . '" name="modules[]" value="' . $row['module_id'] . '">';
                        echo '<label for="module_' . $row['module_id'] . '">' . $row['module_name'] . ' - ' . $row['module_code'] . '</label>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No modules found.</p>";
                }
                ?>
                <input type="submit" value="Save Modules">
            </form>
        </div>
    </main>
   
    <script>
        function searchModules() {
            const query = document.getElementById('search-input').value;
            if (query) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'Modules.php?query=' + encodeURIComponent(query), true);
                xhr.onload = function() {
                    if (this.status === 200) {
                        const modules = JSON.parse(this.responseText);
                        let output = '';
                        if (modules.length > 0) {
                            modules.forEach(module => {
                                output += `
                                    <div class="module-item">
                                        <input type="checkbox" id="module_${module.module_id}" name="modules[]" value="${module.module_id}">
                                        <label for="module_${module.module_id}">${module.module_name} - ${module.module_code}</label>
                                    </div>
                                `;
                            });
                        } else {
                            output = '<p>No modules found.</p>';
                        }
                        document.getElementById('search-results').innerHTML = output;
                    }
                }
                xhr.send();
            } else {
                document.getElementById('search-results').innerHTML = 'Please enter a module name.';
            }
        }
    </script>
</body>
</html>