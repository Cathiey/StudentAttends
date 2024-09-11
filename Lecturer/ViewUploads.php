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

// Get the module for which to display the uploads
$module = isset($_GET['module']) ? $conn->real_escape_string($_GET['module']) : '';

// Fetch uploaded letters for the selected module
$stmt = $conn->prepare("SELECT student_number, file_path, upload_date FROM uploads WHERE module_name = ?");
$stmt->bind_param("s", $module);
$stmt->execute();
$uploadsResult = $stmt->get_result();

$modules = []; // To hold available modules
$moduleStmt = $conn->query("SELECT DISTINCT module_name FROM uploads");
while ($row = $moduleStmt->fetch_assoc()) {
    $modules[] = $row['module_name'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Letters</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: white;
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

        main {
            padding: 100px 20px 20px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
       
        .container {
            padding: 20px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 300px; /* Set a max width */
            margin: 0 auto; /* Center the container */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
        }
        .filters label {
            margin-right: 10px;
            font-size: 14px;
        }
        .filters select {
            padding: 5px;
            font-size: 14px;
        }
        .report-section {
            margin-top: 20px;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .report-header {
            border-bottom: 2px solid #003366;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .report-header h2 {
            font-size: 22px;
            font-weight: bold;
            color: #003366;
            margin: 0;
        }
        .report-header p {
            font-size: 16px;
            margin: 5px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table thead {
            background-color: #003366;
            color: white;
        }
        .report-table th, .report-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .report-table th {
            font-weight: bold;
        }
        .report-table td {
            background-color: #f9f9f9;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f1f1f1;
        }
        .report-table tr:hover td {
            background-color: #e0e0e0;
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
    </style>
</head>
<body>
    <header>
        <h1>Uploaded Letters</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    
    <main>
        <div class="container">
            <!-- Filter Section -->
            <form method="GET" action="">
                <div class="filters">
                    <label for="module">Select Module</label>
                    <select id="module" name="module" onchange="this.form.submit()">
                        <option value="">Select Module</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?php echo htmlspecialchars($mod); ?>" <?php echo $mod === $module ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mod); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <!-- Uploaded Letters Section -->
            <?php if ($module): ?>
                <div class="report-section">
                    <div class="report-header">
                        <h2>Uploaded Letters for "<?php echo htmlspecialchars($module); ?>"</h2>
                    </div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Student Number</th>
                                <th>File</th>
                                <th>Upload Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($uploadsResult->num_rows > 0): ?>
                                <?php while ($row = $uploadsResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                                        <td>
                                            <a href="<?php echo 'http://localhost/' . htmlspecialchars($row['file_path']); ?>" download>Download File</a>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['upload_date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">No files uploaded for this module.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
</body>
</html>
