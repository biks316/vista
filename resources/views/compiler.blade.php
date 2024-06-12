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
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            color: #007BFF;
        }
        .container {
            display: flex;
            flex-direction: row;
            width: 80%;
            justify-content: space-around;
            margin-top: 20px;
        }
        .column {
            flex: 1;
            margin: 10px;
        }
        textarea {
            width: calc(100% - 20px); /* Adjusted width to account for margins */
            height: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            font-family: 'Courier New', Courier, monospace;
            margin-bottom: 10px; /* Added margin-bottom for spacing */
        }
        .output {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            background-color: #f8f8f8;
            overflow-x: auto;
        }
        button {
            margin-top: 10px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        #loading {
            display: none;
            color: #007BFF;
            margin-top: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Online PHP Compiler</h1>
    <div class="container">
        <div class="column">
            <label for="code">Input Code:</label>
            <textarea id="code" placeholder="Write your PHP code here..."></textarea>
            <button onclick="executeCode()">Run Code</button>
            <div id="loading">Running your code...</div>
        </div>
        <div class="column">
            <label for="output">Output:</label>
            <div id="output" class="output"></div>
        </div>
    </div>

    <script>
        function executeCode() {
            const code = document.getElementById('code').value;
            const outputElement = document.getElementById('output');
            const loadingElement = document.getElementById('loading');

            outputElement.innerHTML = '';  // Clear previous output
            loadingElement.style.display = 'block';

            fetch('/php_execute', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code }),
            })
            .then(response => response.json().catch(() => {
                return { output: 'Error: The server returned an invalid response.' };
            }))
            .then(data => {
                loadingElement.style.display = 'none';
                if (data.output) {
                    outputElement.innerHTML = data.output;
                } else {
                    outputElement.innerHTML = 'Error: No output received.';
                }
            })
            .catch(error => {
                loadingElement.style.display = 'none';
                outputElement.innerHTML = 'Error: ' + error;
            });
        }
    </script>
</body>
</html>
