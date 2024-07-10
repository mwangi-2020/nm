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

// Fetch all entries from the exam_questions table
$result = $conn->query("SELECT question1, question2, question3, question4, question5, question6, question7, question8, question9, question10 FROM exam_questions");

// Initialize an array to hold all questions
$allQuestions = [];

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Fetch each row and add the questions to the array
    while ($row = $result->fetch_assoc()) {
        for ($i = 1; $i <= 10; $i++) {
            // Add each non-empty question to the allQuestions array
            $question = $row["question$i"];
            if (!empty($question)) {
                $allQuestions[] = $question;
            }
        }
    }
    // Output each question on a new line
    echo implode("\n", $allQuestions);
} else {
    // Output an error message if no questions were found
    echo "No questions found. Please generate questions first.";
}

// Close the database connection
$conn->close();
?>
