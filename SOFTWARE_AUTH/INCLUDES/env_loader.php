<?php

/*
-------------------------------------------------------------
File: env_loader.php
Description:
- Loads environment variables from a `.env` file into the `$_ENV` superglobal.
- Reads each line, skipping comments, and splits key-value pairs.
- Stores the variables for later use in the application.
-------------------------------------------------------------
*/

// Define the path to the .env file
$envPath = realpath(__DIR__ . '/../.env');

// Check if the .env file exists
if (!file_exists($envPath)) {
    die('.env file not found at: ' . $envPath);
}

// Read the lines of the .env file
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Loop through each line in the file
foreach ($lines as $line) {
    // Skip comment lines (lines starting with #)
    if (strpos(trim($line), '#') === 0) continue;

    // Split the line into a name-value pair
    list($name, $value) = explode('=', $line, 2);

    // Store the variable in the $_ENV superglobal
    $_ENV[trim($name)] = trim($value);
}