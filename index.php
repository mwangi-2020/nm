<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Question Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }

        h1 {
            color: #333;
        }

        input[type="text"], input[type="number"] {
            padding: 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        #questions {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            white-space: pre-wrap;
        }

        #view-questions-btn {
            display: none;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        #view-questions-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>

<body>

    <h1>Enter subject and number of question here</h1>

    <label for="subject">Subject:</label>
    <input type="text" id="subject" placeholder="Enter subject name...">

    <label for="topic">Topic:</label>
    <input type="text" id="topic" placeholder="Enter topic name...">

    <label for="numQuestions">Number of Questions:</label>
    <input type="number" id="numQuestions" placeholder="Enter number of questions...">

    <br><br>

    <button onclick="generateQuestions();">Generate Questions</button>

    <br><br>

    <h2>Generated Questions👇</h2>

    <div id="questions"></div>

    <button id="view-questions-btn" onclick="viewQuestions();">View Questions</button>

    <script>
        function generateQuestions() {
            var subject = document.getElementById("subject").value;
            var topic = document.getElementById("topic").value;
            var numQuestions = parseInt(document.getElementById("numQuestions").value);
            var questionsContainer = document.getElementById("questions");
            var viewQuestionsBtn = document.getElementById("view-questions-btn");

            // Validate input
            if (!subject || !topic || isNaN(numQuestions) || numQuestions <= 0) {
                questionsContainer.innerHTML = "<p>Please fill out all fields correctly.</p>";
                return;
            }

            // Prepare data to send to server
            var requestData = {
                subject: subject,
                topic: topic,
                numQuestions: numQuestions
            };

            // Show loading message
            questionsContainer.innerHTML = "Generating questions...";

            fetch("generate_questions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(requestData),
            })
            .then((res) => res.text())
            .then((res) => {
                // Display generated questions
                questionsContainer.innerHTML = res;

                // Show the view questions button
                viewQuestionsBtn.style.display = "block";
            })
            .catch((error) => {
                console.error('Error:', error);
                questionsContainer.innerHTML = "<p>Failed to generate questions. Please try again later.</p>";
            });
        }

        function viewQuestions() {
            window.location.href = "view_questions.html";
        }
    </script>

</body>

</html>
