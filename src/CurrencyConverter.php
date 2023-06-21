<?php
require_once 'config.php';

class CurrencyConverter {
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

    public function convertCurrency($amount, $sourceCurrency, $targetCurrency) {
        $sourceRate = $this->getCurrencyRate($sourceCurrency, $this->host, $this->user, $this->pass, $this->db);
        $targetRate = $this->getCurrencyRate($targetCurrency, $this->host, $this->user, $this->pass, $this->db);

        if ($sourceRate === null || $targetRate === null) {
            throw new Exception("Currency rate not found.");
        }

        // Convert the amount from the source currency to the target currency
        $convertedAmount = ($amount / $sourceRate) * $targetRate;

        return $convertedAmount;
    }

    private function getCurrencyRate($currencyCode) {
        // Create a connection
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        // Check the connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Fetch the currency rate from the database
        $sql = "SELECT rate FROM currency_rates WHERE currency_code = '$currencyCode'";
        $result = $conn->query($sql);

        if ($result === false) {
            throw new Exception("Query failed: " . $conn->error);
        }

        if ($result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        $rate = $row['rate'];

        // Close the connection
        $conn->close();

        return $rate;
    }
}

$converter = new CurrencyConverter();
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    try {
        $convertedAmount = $converter->convertCurrency($_POST["amount"], $_POST["sourceCurrency"], $_POST["targetCurrency"]);
        echo "Converted amount: {$convertedAmount} {$_POST["targetCurrency"]}";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>