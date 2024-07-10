<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aiquestions";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read JSON data from request
$data = json_decode(file_get_contents("php://input"));

// Validate input
if (isset($data->questions) && is_array($data->questions)) {
    // Extract and clean questions
    $questions = array_map('trim', $data->questions);

    // Ensure we have exactly 10 questions (or adjust according to your needs)
    while (count($questions) < 10) {
        $questions[] = "";
    }

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE exam_questions SET question1 = ?, question2 = ?, question3 = ?, question4 = ?, question5 = ?, question6 = ?, question7 = ?, question8 = ?, question9 = ?, question10 = ? WHERE id = (SELECT id FROM (SELECT id FROM exam_questions ORDER BY id DESC LIMIT 1) as t)");
    $stmt->bind_param("ssssssssss", ...$questions);

    if ($stmt->execute()) {
        echo "Questions updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
} else {
    echo "Invalid input: No questions received or incorrect format.";
}

// Close connection
$conn->close();
?>
