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

// Database connection settings
$host = 'localhost';
$db = 'aiquestions';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Loop through POST data and insert into database
foreach ($_POST as $key => $value) {
    if (strpos($key, 'question') === 0) {
        $index = str_replace('question', '', $key);
        $question = $value;
        $answer = $_POST['answer' . $index];

        // Prepare an SQL statement for inserting data
        $stmt = $conn->prepare("INSERT INTO answers (question, answer, name) VALUES (?, ?, ?)");

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sss", $question, $answer, $student_username);

        // Execute statement
        $stmt->execute();

        // Close the statement
        $stmt->close();
    }
}

// Close the connection
$conn->close();

// Redirect to success.html after successful submission
header('Location: success.html');
exit();
?>
