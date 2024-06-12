<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online PHP Compiler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
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
        .code-editor {
            height: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            font-family: 'Courier New', Courier, monospace;
            margin-bottom: 10px;
        }
        .terminal {
            width: 100%;
            height: 300px;
            background-color: #1e1e1e;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            border: 1px solid #333;
            border-radius: 5px;
            padding: 10px;
            overflow-y: auto;
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
        button:active {
            background-color: #004494;
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
        .runtime-input {
            width: 100%;
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            font-family: 'Courier New', Courier, monospace;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Online PHP Compiler</h1>
    <div class="container">
        <div class="column">
            <label for="code">Input Code:</label>
            <div id="code" class="code-editor"></div>
            <label for="input">Runtime Input:</label>
            <textarea id="input" class="runtime-input" placeholder="Enter runtime input here..."></textarea>
            <button onclick="executeCode()">Run Code</button>
            <div id="loading">Running your code...</div>
        </div>
        <div class="column">
            <label for="output">Output:</label>
            <div id="output" class="terminal"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/css/css.min.js"></script>
    <script>
        const editor = CodeMirror(document.getElementById('code'), {
            mode: 'application/x-httpd-php',
            lineNumbers: true,
            theme: 'default',
            autoCloseTags: true,
            matchTags: { bothTags: true }
        });

        function executeCode() {
            const code = editor.getValue();
            const input = document.getElementById('input').value;
            const outputElement = document.getElementById('output');
            const loadingElement = document.getElementById('loading');

            outputElement.innerHTML = '';  // Clear previous output
            loadingElement.style.display = 'block';

            fetch('/php_execute', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure this token is correctly set by your backend
                },
                body: JSON.stringify({ code: code, input: input }),
            })
            .then(response => response.json().catch(() => {
                return { output: 'Error: The server returned an invalid response.' };
            }))
            .then(data => {
                loadingElement.style.display = 'none';
                if (data.output) {
                    outputElement.innerHTML = `<pre>${data.output}</pre>`;
                } else {
                    outputElement.innerHTML = '<pre>Error: No output received.</pre>';
                }
            })
            .catch(error => {
                loadingElement.style.display = 'none';
                outputElement.innerHTML = '<pre>Error: ' + error.message + '</pre>';
            });
        }
    </script>
</body>
</html>
