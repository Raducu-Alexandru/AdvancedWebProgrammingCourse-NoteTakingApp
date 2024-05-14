<?php
// Database configuration settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'NoteTakingApp');

try {
  // Create PDO instance to connect to the database
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
  // Set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("ERROR: Could not connect. " . $e->getMessage());
}

// Session configuration
session_save_path(__DIR__ . '/../sessions');  // Sets the path where sessions are stored
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 1440); // Sets the session garbage collector max lifetime in seconds

// Start the session with secure settings
session_start([
  'use_strict_mode' => 1,
  'use_cookies' => 1,
  'cookie_secure' => 0, // Set to 1 if using HTTPS, 0 if using HTTP
  'cookie_httponly' => 1
]);

// Ensure sessions are only passed through cookies and not URL parameters
ini_set('session.use_only_cookies', 1);
