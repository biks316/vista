<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class LaravelProjectCreator extends Controller
{
    public static function createProject()
    {

// Example usage
$projectName = 'my_new_laravel_project';
$directory = '/'; // Optional: specify the directory where you want to create the project

$result = LaravelProjectCreator::createProject($projectName, $directory);
echo $result;

        // Define the base directory where the project will be created
        $baseDirectory = $directory ?? getcwd();

        // Construct the full path for the new project
        $projectPath = rtrim($baseDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $projectName;

        // Check if the directory already exists
        if (file_exists($projectPath)) {
            return "Error: Directory already exists at $projectPath";
        }

        // Construct the command to create the Laravel project
        $command = "composer create-project --prefer-dist laravel/laravel $projectPath";

        // Execute the command and capture the output
        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        // Check if the command was successful
        if ($returnVar === 0) {
            return "Success: Laravel project created at $projectPath";
        } else {
            return "Error: Failed to create Laravel project. Output: " . implode("\n", $output);
        }
    }



}