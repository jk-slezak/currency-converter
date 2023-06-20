<?php
//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = 'db';

// Database use name
$user = 'devuser';

//database user password
$pass = 'devpass';

// database name
$db = 'test_db';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "Connected to MySQL server successfully!\n";

// Close the connection
$conn->close();
?>