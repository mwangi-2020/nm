<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    // Redirect to login page if not logged in
    header('Location: student_login.php');
    exit;
}

// Retrieve student information from session
$student_id = $_SESSION['student_id'];
$student_username = $_SESSION['student_username']; // Assuming you stored username in session

// Database connection details (replace with your actual database credentials)
$host = 'localhost'; // or your host
$dbname = 'studentsnames'; // your database name
$username_db = 'root'; // your database username
$password_db = ''; // your database password

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to retrieve student performance data (example)
function getStudentPerformance($pdo, $student_id) {
    $stmt = $pdo->prepare("SELECT * FROM student_performance WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Example function to get student's name from database
function getStudentName($pdo, $student_id) {
    $stmt = $pdo->prepare("SELECT username FROM students WHERE id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    return $student['username'];
}

// Get student's name from database (example)
$student_name = getStudentName($pdo, $student_id);

// Get student's performance data (example)
$performance_data = getStudentPerformance($pdo, $student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .dashboard-container {
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .welcome-message {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .performance-section {
            margin-bottom: 30px;
        }
        
        .performance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .performance-table th, .performance-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        
        .performance-table th {
            background-color: #f2f2f2;
        }
        
        .button-container {
            text-align: center;
        }
        
        .button-container a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        
        .button-container a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome to Student Dashboard</h2>
        <div class="welcome-message">
            <p>Welcome, <?php echo htmlspecialchars($student_name); ?>!</p>
        </div>
        
        <div class="performance-section">
            <h3>Performance Summary</h3>
            <?php if (count($performance_data) > 0): ?>
                <table class="performance-table">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Score</th>
                            <th>Date Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($performance_data as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['test_name']); ?></td>
                                <td><?php echo htmlspecialchars($data['score']); ?></td>
                                <td><?php echo htmlspecialchars($data['date_taken']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No performance data available.</p>
            <?php endif; ?>
        </div>
        
        <div class="button-container">
            <a href="student_page.html">Do a Test</a>
            <a href="view_performance.php">View My Performance</a>
            <a href="student_login.php">Logout</a>
        </div>
    </div>
</body>
</html>
