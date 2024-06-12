<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Main extends Controller
{
    //


public function phpprocess(Request $req){

        $code = $request->input('code', '');

        // Ensure the code does not contain any dangerous commands
        $forbidden_functions = ['exec', 'shell_exec', 'system', 'passthru', 'popen', 'proc_open'];
        foreach ($forbidden_functions as $function) {
            if (stripos($code, $function) !== false) {
                return response()->json(['output' => 'Error: Use of forbidden functions detected.'], 400);
            }
        }

        // Save the code to a temporary file
        $tmp_file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tmp_file, "<?php\n" . $code);

        // Execute the code and capture the output
        ob_start();
        include $tmp_file;
        $output = ob_get_clean();

        // Remove the temporary file
        unlink($tmp_file);

        // Output the result
        return response()->json(['output' => $output]);

}
