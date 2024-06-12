<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CompilerController extends Controller
{
    // Define keywords and patterns for each language
    private $languages = [
         'php' => [
            'keywords' => ['<?php', 'echo', '->', '::', 'function ', '$'],
            'patterns' => ['/^\<\?php/', '/echo\s/', '/\$\w+/', '/function\s/', '/->/', '/::/']
        ],
        'c' => [
            'keywords' => ['#include', 'int main', 'printf', 'scanf'],
            'patterns' => ['/^\#include/', '/int\s+main/', '/printf\s*\(/', '/scanf\s*\(/']
        ],
        'python' => [
            'keywords' => ['import ', 'def ', 'print(', 'class '],
            'patterns' => ['/^\s*def\s/', '/^\s*class\s/', '/^import\s/', '/print\s*\(/']
        ],
        'javascript' => [
            'keywords' => ['function ', 'console.log', 'var ', 'let ', 'const '],
            'patterns' => ['/function\s/', '/console\.log/', '/\b(var|let|const)\s+/']
        ],
        'c++' => [
            'keywords' => ['#include', 'iostream', 'cin', 'cout'],
            'patterns' => ['/^\#include/', '/iostream/', '/\bcin\b/', '/\bcout\b/']
        ],
        // Add more languages as needed
    ];

    public function index()
    {
        return view('compiler');
    }

    public function unknown_execute(Request $req)
    {
        $code = $req->input('code');
        $language = $this->detectLanguage($code);

        return response()->json(['output' => $language]);

           switch ($language) {
            case 'c':
                return $this->executeCCode($code);
            case 'php':
                return $this->executePHPCode($code);
            case 'python':
                return $this->executePythonCode($code);
            case 'javascript':
                return $this->executeJavaScriptCode($code);
            case 'c++':
                return $this->executeCplusCode($code);
            default:
                return response()->json(['output' => 'Unsupported language: ' . $language], 400);
        }


    }

    public function compileAndExecute(Request $request)
    {
        $code = $request->input('code');
        

        // Detect the programming language
        $language = $this->detectLanguage($code);

        switch ($language) {
            case 'c':
            $output=$this->executeCCode($code);
                //return $output;
                return response()->json(['output'=>'<u>Language: C<br></u>'.$output]);
              break ;
            case 'php': 
                $output=$this->executePHPCode($code);
                return response()->json(['output'=>'<u>Language: PHP<br></u>'.$output]);
               break;
            case 'python':
            
                $output=$this->executePythonCode($code);
                return response()->json(['output'=>$output]);
            break;
            case 'javascript':
            $output=$this->executeJavascriptCode($code);
                return response()->json(['output'=>$output]);
            break;
            default:
                return response()->json(['output' => 'Unsupported language: ' . $language], 400);
        }
    }

    private function detectLanguage($code)
    {
        // Check for keywords and patterns
        foreach ($this->languages as $language => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (stripos($code, $keyword) !== false) {
                    return $language;
                }
            }

            foreach ($data['patterns'] as $pattern) {
                if (preg_match($pattern, $code)) {
                    return $language;
                }
            }
        }

        return 'unknown';
    }


    public function executeCplusCode($code){

        return '102';

    }

 public function executeCCode($code)
{
    $filePath = storage_path('app/code.c');

    // Save the code to a temporary file
    file_put_contents($filePath, $code);

    // Compile the C code
    $outputFilePath = storage_path('app/output');
    $process = new Process(['gcc', $filePath, '-o', $outputFilePath]);
    
    $process->run();

    // Check if the compilation was successful
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    // Execute the compiled program
    $process = new Process([$outputFilePath]);

    // Set a time limit for the execution (5 seconds in this example)
    $process->setTimeout(5);

    $process->start();

    // Wait until the process completes or timeout occurs
    $process->wait();

    // Check if the process timed out
    if ($process->isRunning()) {
        // Process timed out
        $process->stop(0); // Forcefully terminate the process
        $output = "Execution timed out after 5 seconds.";
        
    } else {
        // Process completed within the time limit
        $output = $process->getOutput();
    }

    return $output;
}


    private function executePHPCode($code)
    {

        
        //$code = $request->input('code', '');
        //$input = $request->input('input', '');

        // Ensure the code does not contain any dangerous commands
        $forbidden_functions = ['exec', 'shell_exec', 'system', 'passthru', 'popen', 'proc_open', 'pcntl_exec'];
        foreach ($forbidden_functions as $function) {
            if (stripos($code, $function) !== false) {
                return response()->json(['output' => 'Error: Use of forbidden functions detected.'], 400);
            }
        }

        $tmp_file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tmp_file, "\n" . $code);

        ob_start();
        pcntl_async_signals(true);
        pcntl_alarm(5);

        try {
            include $tmp_file;
            $output = ob_get_clean();
        } catch (\Exception $e) {
            $output = 'Error: ' . $e->getMessage();
        } catch (\Throwable $t) {
            $output = 'Error: ' . $t->getMessage();
        } finally {
            pcntl_alarm(0);
            unlink($tmp_file);
        }

        return $output;
        //return response()->json(['output' => $output]);
    }

    private function executePythonCode($code)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'python_code_');
        file_put_contents($filePath . '.py', $code);

        $command = "echo \"$input\" | python3 $filePath";
        $output = $this->runCommandWithTimeout($command, 5);

        return response()->json(['output' => implode("\n", $output)]);
    }

    private function executeJavaScriptCode($code)
    {

        
        $filePath = tempnam(sys_get_temp_dir(), 'js_code_');
        file_put_contents($filePath . '.js', $code);

        $command = "echo \"$input\" | node $filePath";
        $output = $this->runCommandWithTimeout($command, 5);

        return $output;
        //return response()->json(['output' => implode("\n", $output)]);
    }

    private function runCommandWithTimeout($command, $timeout)
    {
        $descriptors = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"], // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            return ["Error: Unable to execute command."];
        }

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $startTime = microtime(true);
        $output = '';

        while (true) {
            $status = proc_get_status($process);
            $runtime = microtime(true) - $startTime;

            if (!$status['running'] || $runtime > $timeout) {
                proc_terminate($process);
                $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
                if ($runtime > $timeout) {
                    $output .= "\nExecution timed out.";
                }
                break;
            }
            usleep(10000); // Sleep for 10ms
        }

        foreach ($pipes as $pipe) {
            fclose($pipe);
        }

        proc_close($process);

        return explode("\n", $output);
    }
}
