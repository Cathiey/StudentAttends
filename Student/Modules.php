<?php
session_start();

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

// Fetch course for the logged-in user
$studentNumber = $_SESSION['studentNumber']; // Assuming this session variable contains the student number
$sql = "SELECT course FROM students WHERE studentNumber = '$studentNumber'";
$result = $conn->query($sql);

// Check if a course is found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $course = $row['course'];
} else {
    // Handle case where course is not found (optional)
    $course = "Unknown"; // Default course
}

// Fetch modules based on the retrieved course
$sql = "SELECT m.module_id, m.module_name, m.module_code 
        FROM modules m 
        JOIN module_courses mc ON m.module_id = mc.module_id 
        JOIN courses c ON mc.course_id = c.course_id 
        WHERE c.course_name = '$course'";

$result = $conn->query($sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modules'])) {
    $studentNumber = $_SESSION['studentNumber'];
    $modules = $_POST['modules'];

    foreach ($modules as $module_id) {
        // Check if the module is already registered for the student
        $checkSQL = "SELECT * FROM student_modules WHERE student_number = '$studentNumber' AND module_id = '$module_id'";
        $checkResult = $conn->query($checkSQL);

        if ($checkResult->num_rows > 0) {
            // If module already exists for the student, alert the user
            echo "<script>alert('Module with ID $module_id already exists for this student.');window.location.href = 'Modules.php';</script>";
        } else {
            // Insert the module if it doesn't already exist
            $insertSQL = "INSERT INTO student_modules (student_number, module_id) VALUES ('$studentNumber', '$module_id')";
            if ($conn->query($insertSQL) === TRUE) {
                // After inserting, redirect or notify the user
    echo "<script>
    alert('Modules saved successfully');
    window.location.href = 'StudentHome.php';
</script>";
            } else {
                // Handle any errors if needed
                echo "<script>alert('Error occurred while saving modules: " . $conn->error . "');</script>";
            }
        }
    }

    
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
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

        main {
            padding: 160px 20px 20px 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .container {
            max-width: 800px;
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
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
            display: flex;
            justify-content: center;
            position: fixed;
            top: 60px;
            width: 100%;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.15), rgba(144, 178, 216, 0.15), rgba(193, 227, 255, 0.15));
            z-index: 999;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            color: #122B40;
        }

        /* Modal for displaying search results */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            max-width: 90%;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 20px;
            overflow-y: auto;
            max-height: 80%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .modal-header button {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: #333;
        }

        .modal-body {
            padding: 10px 0;
        }

        .modal-body p {
            margin: 0;
        }

        .modal-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
    <h1>Module Selection</h1>
        <a class="back-button" href="StudentHome.php">Back</a>
    </header>
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search modules...">
        <button onclick="searchModules()">Search</button>
    </div>

    <main>
        <div class="container">
            <h2>Select Modules for <?php echo $course; ?></h2>
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
                    echo "<p>No modules found for this course.</p>";
                }
                ?>
                <input type="submit" value="Save Modules">
            </form>
        </div>
    </main>

    <!-- Modal for displaying search results -->
    <div id="modal" class="modal">
        <div class="modal-header">
            <h3>Search Results</h3>
            <button onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="search-results"></div>
    </div>

    <script>
        function searchModules() {
            const query = document.getElementById('search-input').value;
            if (query) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'Modules.php?query=' + encodeURIComponent(query), true);
                xhr.onload = function () {
                    if (this.status === 200) {
                        const modules = JSON.parse(this.responseText);
                        let output = '';
                        if (modules.length > 0) {
                            modules.forEach(module => {
                                output += `
                                    <div class="modal-item">
                                        <input type="checkbox" id="module_${module.module_id}" name="modules[]" value="${module.module_id}">
                                        <label for="module_${module.module_id}">${module.module_name} - ${module.module_code}</label>
                                    </div>
                                `;
                            });
                        } else {
                            output = '<p>No modules found.</p>';
                        }
                        document.getElementById('search-results').innerHTML = output;
                        openModal(); // Open the modal after search results are populated
                    }
                }
                xhr.send();
            } else {
                document.getElementById('search-results').innerHTML = '<p>Please enter a module name.</p>';
            }
        }

        function openModal() {
            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Close the modal when clicking outside of it
        window.onclick = function (event) {
            const modal = document.getElementById('modal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
