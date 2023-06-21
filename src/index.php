<!DOCTYPE html>
<html>
<head>
    <title>Currency Conversion</title>
</head>
<body>
    <h1>Currency Conversion</h1>
    <form method="POST" action="CurrencyConverter.php">
        <label for="amount">Amount:</label>
        <input type="number" step="0.01" name="amount" required><br><br>

        <label for="sourceCurrency">Source Currency:</label>
        <select name="sourceCurrency" required>
            <option name="USD" value="USD">USD</option>
            <option name="EUR" value="EUR">EUR</option>
        </select><br><br>

        <label for="targetCurrency">Target Currency:</label>
        <select name="targetCurrency" required>
            <option name="USD" value="USD">USD</option>
            <option name="EUR" value="EUR">EUR</option>
        </select><br><br>

        <input type="submit" value="Convert">
    </form>
</body>
</html>

<?php
require_once 'config.php';
require_once 'NBPApiClient.php';
require_once 'CurrencyRateTableGenerator.php';
require_once 'CurrencyConverter.php';

class main {
    private $host;
    private $user;
    private $pass;
    private $db;

    public function __construct() {
        // Database connection parameters
        $this->host = DB_HOST;
        $this->user = DB_USERNAME;
        $this->pass = DB_PASSWORD;
        $this->db = DB_NAME;
    }

    public function run() {
        // Create a connection to the database
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 

        // Fetch currency rates from the NBP API
        $client = new NBPApiClient();

        try {
            $client->getCurrencyRates();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        // Generate the table
        $tableGenerator = new CurrencyRateTableGenerator();

        try {
            $table = $tableGenerator->generateTable();
            echo $table;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        // Close the connection
        $conn->close();
    }
}

$main = new main();
$main->run();
?>