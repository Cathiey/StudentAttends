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

// Handle lecturer assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['module_id']) && isset($_POST['lecturer_id'])) {
    $module_id = $_POST['module_id'];
    $lecturer_id = $_POST['lecturer_id'];

    // Replace existing lecturer assignment or insert new one
    $sql = "REPLACE INTO lecturer_modules (module_id, lecturer_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $module_id, $lecturer_id);

    if ($stmt->execute()) {
        // Fetch the lecturer's name
        $sql = "SELECT CONCAT(first_name, ' ', last_name) AS lecturer_name FROM lecturers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $lecturer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $lecturer = $result->fetch_assoc();
        $lecturer_name = $lecturer['lecturer_name'];

        echo json_encode([
            'status' => 'success',
            'lecturer_name' => $lecturer_name
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error assigning lecturer: ' . $stmt->error
        ]);
    }

    $stmt->close();
    exit; // Exit to prevent further code execution
}

// Fetch modules and their respective lecturers from the database
$sql = "SELECT m.module_name, m.module_code, CONCAT(l.first_name, ' ', l.last_name) AS lecturer_name, m.module_id
        FROM Modules m
        LEFT JOIN lecturer_modules lm ON m.module_id = lm.module_id
        LEFT JOIN lecturers l ON lm.lecturer_id = l.id";
$modules = $conn->query($sql);

// Fetch all lecturers from the database for assignment
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS lecturer_name FROM lecturers";
$lecturers = $conn->query($sql);

// Fetch lecturers into an array
$lecturer_options = [];
while ($lecturer = $lecturers->fetch_assoc()) {
    $lecturer_options[] = $lecturer;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module List</title>
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
            text-align: center;
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
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(120deg, #122B40, #446CB3);
            margin-top: auto;
        }

        footer a {
            color: #122B40;
            text-decoration: none;
        }

        .form-inline {
            display: flex;
            align-items: center;
        }

        .form-inline select,
        .form-inline button {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <header>
        &nbsp;<h1>Modules</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <div class="container">
        <h2>List of Modules</h2>
        <table>
            <thead>
                <tr>
                    <th>Module Name</th>
                    <th>Module Code</th>
                    <th>Lecturer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($modules->num_rows > 0): ?>
                    <?php while($row = $modules->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['module_code']); ?></td>
                            <td class="lecturer-name" data-module-id="<?php echo htmlspecialchars($row['module_id']); ?>">
                                <?php echo htmlspecialchars($row['lecturer_name']) ?: 'Not Assigned'; ?>
                            </td>
                            <td>
                                <form class="form-inline">
                                    <input type="hidden" name="module_id" value="<?php echo htmlspecialchars($row['module_id']); ?>">
                                    <select name="lecturer_id">
                                        <option value="">Select Lecturer</option>
                                        <?php foreach ($lecturer_options as $lecturer): ?>
                                            <option value="<?php echo htmlspecialchars($lecturer['id']); ?>">
                                                <?php echo htmlspecialchars($lecturer['lecturer_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="assign-button">Assign</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No modules found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <footer>
    </footer>

    <!-- Include jQuery for simplicity -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.assign-button').click(function() {
                var form = $(this).closest('form');
                var module_id = form.find('input[name="module_id"]').val();
                var lecturer_id = form.find('select[name="lecturer_id"]').val();
                var lecturer_name_cell = form.closest('tr').find('.lecturer-name');

                $.ajax({
                    url: '',
                    method: 'POST',
                    data: {
                        module_id: module_id,
                        lecturer_id: lecturer_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            lecturer_name_cell.text(response.lecturer_name);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
