<?php
require "vendor/autoload.php";

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

// Function to calculate the overall grade based on percentage
function calculateOverallGrade($percentage) {
    if ($percentage >= 95) {
        return 'A';
    } elseif ($percentage >= 90) {
        return 'A-';
    } elseif ($percentage >= 85) {
        return 'B+';
    } elseif ($percentage >= 80) {
        return 'B';
    } elseif ($percentage >= 75) {
        return 'B-';
    } elseif ($percentage >= 70) {
        return 'C+';
    } elseif ($percentage >= 65) {
        return 'C';
    } elseif ($percentage >= 60) {
        return 'C-';
    } elseif ($percentage >= 55) {
        return 'D+';
    } elseif ($percentage >= 50) {
        return 'D';
    } elseif ($percentage >= 45) {
        return 'D-';
    } else {
        return 'F';
    }
}

// Main logic to grade answers based on marked answers
try {
    // Retrieve questions and answers from the database
    $questionsAndAnswers = retrieveQuestionsAndAnswers();

    // Process each answer to extract grade and determine marks
    $conn = getDatabaseConnection();
    $updateSql = "UPDATE answers SET marks = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);

    $totalMarks = 0;
    $numQuestions = count($questionsAndAnswers);
    $maxMarksPerQuestion = 12;
    $maxTotalMarks = $numQuestions * $maxMarksPerQuestion;

    foreach ($questionsAndAnswers as $qa) {
        // Extract grade from marked_answer
        preg_match('/\b[A-F][+-]?\b/', $qa['marked_answer'], $matches);
        $grade = $matches[0] ?? '';

        // Determine marks based on grade
        $marks = determineMarks($grade);

        // Update marks in the database
        $updateStmt->bind_param('ii', $marks, $qa['id']);
        $updateStmt->execute();

        // Accumulate total marks
        $totalMarks += $marks;
    }

    $updateStmt->close();
    $conn->close();

    // Calculate percentage
    $percentage = ($totalMarks / $maxTotalMarks) * 100;

    // Calculate overall grade
    $overallGrade = calculateOverallGrade($percentage);

    // Display overall grade and total marks to the user
    echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";
    echo "<h1 style='color: #514caf;'>Grade Results</h1>";
    echo "<p style='font-size: 18px;'><strong>Overall Grade:</strong> <span style='color: #FF5722;'>" . $overallGrade . "</span></p>";
    echo "<p style='font-size: 18px;'><strong>Total Marks:</strong> " . $totalMarks . "</p>";
    echo "<p style='font-size: 18px;'><strong>Percentage:</strong> " . number_format($percentage, 2) . "%</p>";
    echo "</div>";

} catch (Exception $e) {
    // Handle errors
    http_response_code(500);
    echo 'Error processing grades: ' . $e->getMessage();
}
?>
