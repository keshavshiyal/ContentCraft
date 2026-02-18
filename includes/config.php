<?php

// includes/config.php

// Define the Root Path (File System)
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));

// Define the Base URL (Web Address)
// This automatically detects if you are on localhost or a real server
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Detect the folder where the project lives
// If your project is at http://localhost/ContentCraft/, this logic finds it.
$script_name = dirname($_SERVER['SCRIPT_NAME']);

// Remove subfolders like '/plans' or '/includes' to get the clean root
$script_name = str_replace(array('/includes', '/templates', '/plans', '/materials', '/tools'), '', $script_name);

// Ensure trailing slash
$base_url = rtrim($protocol . "://" . $host . $script_name, '/\\') . '/';

define('BASE_URL', $base_url);

// AI Configuration
// Get your key from: https://aistudio.google.com/app/apikey
// API KEY — Always keep private, never expose publicly
define('GEMINI_API_KEY', 'Your-API-KEY-HERE'); 
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent');

// Control Output Length (Default: 50,000)
define('GEMINI_MAX_TOKENS', 50000);
