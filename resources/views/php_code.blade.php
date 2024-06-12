<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online PHP Compiler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        textarea, pre {
            width: 100%;
            height: 300px;
        }
        button {
            margin-top: 10px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Online PHP Compiler</h1>
    <textarea id="code" placeholder="Write your PHP code here..."></textarea>
    <button onclick="executeCode()">Run Code</button>
    <h2>Output:</h2>
    <pre id="output"></pre>

    <script>
        function executeCode() {
            const code = document.getElementById('code').value;

            fetch('execute.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'code=' + encodeURIComponent(code),
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('output').textContent = data;
            })
            .catch(error => {
                document.getElementById('output').textContent = 'Error: ' + error;
            });
        }
    </script>
</body>
</html>
