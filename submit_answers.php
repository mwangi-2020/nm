<?php

// Ensure Composer autoload is included properly
require "vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Read JSON data from request
$data = json_decode(file_get_contents("php://input"));

// Validate input
if (!$data || !isset($data->questions) || !isset($data->answers)) {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid input'));
    exit;
}

// Extract questions and answers from JSON data
$questions = $data->questions;
$answers = (array)$data->answers;

// Initialize GeminiAPI client with your API key
$apiKey = "AIzaSyCXgO6j5ftABxFQ2MKKiqWOvkWDRdm_MHw";
$client = new Client($apiKey);

// Prepare the text for GeminiAPI
$combinedText = "";
foreach ($questions as $index => $question) {
    $answer = isset($answers["answer" . ($index + 1)]) ? $answers["answer" . ($index + 1)] : '';
    $combinedText .= "Question: $question\nAnswer: $answer\n\n";
}

try {
    // Generate content using GeminiAPI based on the combined text
    $response = $client->geminiPro()->generateContent(
        new TextPart("$combinedText - For each question, award 1 mark for correct answers and 0 marks for incorrect answers.")
    );

    // Extract and parse the response text to get the score
    $responseText = trim($response->text());
    
    // Assuming the AI returns scores in a format like "Question 1: 1 mark", "Question 2: 0 marks", etc.
    preg_match_all('/Question \d+: (\d+) mark/', $responseText, $matches);
    $scores = array_map('intval', $matches[1]);

    // Calculate total score
    $totalScore = array_sum($scores);

    // Return total score and generated content as JSON response
    echo json_encode(array('score' => $totalScore, 'generatedContent' => $responseText));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Failed to generate content: ' . $e->getMessage()));
    exit;
}

?>