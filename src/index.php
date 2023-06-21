<?php
require_once 'config.php';
require_once 'NBPApiClient.php';
require_once 'CurrencyRateTableGenerator.php';

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
        // NBP API parameters
        $table = 'A';
        $currencyCode = 'USD';
        $startDate = '2023-06-01';
        $endDate = '2023-06-19';

        // Create a connection to the database
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 

        echo "Connected to MySQL server successfully!\n";

        // Fetch currency rates from the NBP API
        $client = new NBPApiClient();

        try {
            $currencyRates = $client->getCurrencyRates($table, $currencyCode, $startDate, $endDate, $this->host, $this->user, $this->pass, $this->db);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }


        // Generate the table
        $tableGenerator = new CurrencyRateTableGenerator();

        try {
            $table = $tableGenerator->generateTable($currencyCode, $this->host, $this->user, $this->pass, $this->db);
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