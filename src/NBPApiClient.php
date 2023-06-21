<?php
require_once 'config.php';

class NBPApiClient {
    private $baseURL = 'http://api.nbp.pl/api/exchangerates';
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
    /**
     * Fetches currency rates from the NBP API.
     * 
     * @param string $table     Table type, e.g., A, B, C - Table A provides average exchange rates
     * @param string $code      Currency code, e.g., USD, EUR
     * @param string $startDate Start date of the currency rates
     * @param string $endDate   End date of the currency rates
     */
    public function getCurrencyRates($table, $currencyCode, $startDate, $endDate) {
        $url = "{$this->baseURL}/rates/{$table}/{$currencyCode}/{$startDate}/{$endDate}/";
        
        // Initialize the cURL session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the request
        $response = curl_exec($curl);
        
        if ($response === false) {
            throw new Exception(curl_error($curl));
        }
        
        // Check the HTTP status code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception("API request failed with HTTP status code: {$httpCode}");
        }
        
        curl_close($curl);
        
        // Decode the JSON response
        $data = json_decode($response, true);

        // Write data to the database
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }
        
        // Insert currency rates into the database
        foreach ($data['rates'] as $rate) {
            $date = $rate['effectiveDate'];
            $rateValue = $rate['mid'];
            
            $sql = "INSERT INTO currency_rates (currency_code, date, rate) VALUES ('$currencyCode', '$date', $rateValue)";
            
            if ($conn->query($sql) !== true) {
                throw new Exception("Error inserting data: " . $conn->error);
            }
        }
        
        $conn->close();
        
        return $data;
    }
}
?>
