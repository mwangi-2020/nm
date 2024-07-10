<?php
// Database connection settings
$servername = "localhost"; // Replace with your database server name if different
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "exam_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Escape username to prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Query to fetch examiner details from database
    $sql = "SELECT * FROM examiners WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Examiner found, check password
        $examiner = $result->fetch_assoc();
        if ($password == $examiner['password']) {
            // Password is correct, redirect to generate_questions.php
            header("Location: examinerdashboard.html");
            exit;
        } else {
            // Password incorrect
            echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'login.html';</script>";
            exit;
        }
    } else {
        // Examiner not found
        echo "<script>alert('Examiner not found. Please try again.'); window.location.href = 'login.html';</script>";
        exit;
    }
}

$conn->close();
?>
