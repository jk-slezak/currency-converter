<?php
require_once 'NBPApiClient.php';
require_once 'CurrencyRateTableGenerator.php';

// Database connection parameters
$host = 'db';
$user = 'devuser';
$pass = 'devpass';
$db = 'test_db';

// NBP API parameters
$table = 'A';
$currencyCode = 'USD';
$startDate = '2023-06-01';
$endDate = '2023-06-19';

// Create a connection to the database
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "Connected to MySQL server successfully!\n";

// Fetch currency rates from the NBP API
$client = new NBPApiClient();

try {
    $currencyRates = $client->getCurrencyRates($table, $currencyCode, $startDate, $endDate, $host, $user, $pass, $db);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}


// Generate the table
$tableGenerator = new CurrencyRateTableGenerator();

try {
    $table = $tableGenerator->generateTable($currencyCode, $host, $user, $pass, $db);
    echo $table;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>