<?php
require_once 'config.php';

class CurrencyRateTableGenerator {
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
     * Generates a table with currency rates.
     * 
     * @param string $currencyCode Currency code, e.g., USD, EUR
     */
    public function generateTable($currencyCode) {
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT date, rate FROM currency_rates WHERE currency_code = '$currencyCode'";
    
        // Fetch currency rates from the database
        $result = $conn->query($sql);

        // Generate the HTML table
        $table = "<table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $date = $row['date'];
                $rate = $row['rate'];

                $table .= "<tr>
                              <td>$date</td>
                              <td>$rate</td>
                           </tr>";
            }
        } else {
            $table .= "<tr><td colspan='2'>No currency rates found.</td></tr>";
        }
        
        $table .= "</tbody></table>";

        $conn->close();

        return $table;
    }
}
?>