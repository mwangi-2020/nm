<?php
require "vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Function to establish a database connection
function getDatabaseConnection() {
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

    return $conn;
}

// Function to retrieve questions and answers from the database
function retrieveQuestionsAndAnswers() {
    $conn = getDatabaseConnection();
    $sql = "SELECT id, question, answer, marked_answer FROM answers";
    $result = $conn->query($sql);
    $questionsAndAnswers = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questionsAndAnswers[] = [
                'id' => $row['id'],
                'question' => $row['question'],
                'answer' => $row['answer'],
                'marked_answer' => $row['marked_answer']
            ];
        }
    }

    $conn->close();
    return $questionsAndAnswers;
}

// Function to determine marks based on grade
function determineMarks($grade) {
    switch ($grade) {
        case 'A':
            return 12;
        case 'A-':
            return 11;
        case 'B+':
            return 10;
        case 'B':
            return 9;
        case 'B-':
            return 8;
        case 'C+':
            return 7;
        case 'C':
            return 6;
        case 'C-':
            return 5;
        case 'D+':
            return 4;
        case 'D':
            return 3;
        case 'D-':
            return 2;
        case 'F':
            return 1;
        default:
            return 0; // Default case for any other grade or error
    }
}

// Function to mark answers using Gemini AI
function markAnswers($questionsAndAnswers) {
    $client = new Client("AIzaSyCXgO6j5ftABxFQ2MKKiqWOvkWDRdm_MHw"); // Replace with your Gemini API key
    $markedAnswers = [];

    foreach ($questionsAndAnswers as $qa) {
        $questionText = $qa['question'];
        $studentAnswer = $qa['answer'];
        $text = "Grade the following answer give grade only not explanation:\n\nQuestion: $questionText\n\nAnswer: $studentAnswer";

        try {
            $response = $client->geminiPro()->generateContent(new TextPart($text));
            $markedAnswer = $response->text();

            // Extract grade from the response (assuming it is included in the response text)
            preg_match('/Grade:\s*([A-F][+-]?)/i', $markedAnswer, $matches);
            $grade = $matches[1] ?? '';

            // Determine marks based on grade
            $marks = determineMarks($grade);

            $markedAnswers[] = [
                'id' => $qa['id'],
                'question_text' => $questionText,
                'student_answer' => $studentAnswer,
                'marked_answer' => $markedAnswer,
                'marks' => $marks
            ];
        } catch (Exception $e) {
            $markedAnswers[] = [
                'id' => $qa['id'],
                'question_text' => $questionText,
                'student_answer' => $studentAnswer,
                'marked_answer' => 'Failed to mark answer: ' . $e->getMessage(),
                'marks' => 0 // Assign 0 marks if marking fails
            ];
        }
    }

    return $markedAnswers;
}

// Main logic to mark and update answers
try {
    // Retrieve questions and answers from the database
    $questionsAndAnswers = retrieveQuestionsAndAnswers();

    // Mark answers using Gemini API
    $markedAnswers = markAnswers($questionsAndAnswers);

    // Update marked answers and marks in the database
    $conn = getDatabaseConnection();
    $updateSql = "UPDATE answers SET marked_answer = ?, marks = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);

    foreach ($markedAnswers as $answer) {
        $updateStmt->bind_param('sii', $answer['marked_answer'], $answer['marks'], $answer['id']);
        $updateStmt->execute();
    }

    $updateStmt->close();
    $conn->close();

    // Redirect to success page with message
    header("Location: results_ready.html");
    exit;
} catch (Exception $e) {
    // Handle errors
    http_response_code(500);
    echo 'Error marking or updating answers: ' . $e->getMessage();
}
?>
