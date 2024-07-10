<?php
// Start session
session_start();

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

// Function to register a new student (plaintext password)
function registerStudent($pdo, $username, $password) {
    // Insert new student into database (plaintext password)
    $stmt = $pdo->prepare("INSERT INTO students (username, password) VALUES (:username, :password)");
    $stmt->execute(['username' => $username, 'password' => $password]);
}

// Function to validate student credentials (plaintext password)
function validateStudentCredentials($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password from the form
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate student credentials
    $student = validateStudentCredentials($pdo, $username, $password);

    if ($student) {
        // Authentication successful, start session
        $_SESSION['student_id'] = $student['id']; // Store student ID or other relevant information in session
        $_SESSION['student_username'] = $student['username']; // Store username in session
        // Redirect to student dashboard
        header('Location: student_dashboard.php');
        exit;
    } else {
        // Authentication failed, redirect back to login page with error message
        header('Location: student_login.php?error=invalid_credentials');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
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
        
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .login-form {
            text-align: center;
        }
        
        h2 {
            margin-bottom: 20px;
        }
        
        .input-group {
            margin-bottom: 15px;
        }
        
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form action="student_login.php" method="POST" class="login-form">
            <h2>Student Login</h2>
            <?php
            // Display error message if credentials were invalid
            if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials') {
                echo '<p class="error-message">Invalid username or password. Please try again.</p>';
            }
            ?>
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
