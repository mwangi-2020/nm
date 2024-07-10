<?php
session_start();
require "vendor/autoload.php";
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

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

// Extract data
$subject = $data->subject;
$topic = $data->topic;
$numQuestions = $data->numQuestions;

// Validate input
if (!$subject || !$topic || !is_numeric($numQuestions) || $numQuestions <= 0) {
    echo "<p>Invalid input data. Please provide subject, topic, and a valid number of questions.</p>";
    exit;
}

// Initialize GeminiAPI client
$client = new Client("AIzaSyCXgO6j5ftABxFQ2MKKiqWOvkWDRdm_MHw");

// Generate questions using GeminiAPI
$response = $client->geminiPro()->generateContent(
    new TextPart("$subject: $topic - Generate $numQuestions exam questions. Do not put a heading and each question in its own paragraph, also leave a space between each question in this format:
    1. question one
    2. question two
    and so on.")
);

// Extract questions
$questions = explode("\n", trim($response->text()));
$questions = array_map('trim', $questions);

// Split the questions into sets of 10
$sets = array_chunk($questions, 10);

// Insert each set of questions as a new row
foreach ($sets as $set) {
    // Ensure each set has exactly 10 questions
    while (count($set) < 10) {
        $set[] = "";
    }

    // Prepare and execute insert query
    $stmt = $conn->prepare("INSERT INTO exam_questions (question1, question2, question3, question4, question5, question6, question7, question8, question9, question10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", ...$set);

    if ($stmt->execute()) {
        echo "Set of questions generated and saved successfully.<br>";
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
