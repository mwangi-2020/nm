<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        table {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Marked Answers</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Student Answer</th>
                <th>Marked Answer</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Replace these with your actual database credentials
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

            // Fetch data from database
            $sql = "SELECT id, question, answer, marked_answer FROM answers";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['question'] . "</td>";
                    echo "<td>" . $row['answer'] . "</td>";
                    echo "<td>" . $row['marked_answer'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No answers found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
    <a href="grades.php" class="btn">See Grades</a>
    <p><a href="success.html">Back to Home</a></p>
</body>
</html>