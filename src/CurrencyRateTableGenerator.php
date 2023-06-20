<?php
/**
 * Generates a table with currency rates.
 * 
 * @param string $currencyCode Currency code, e.g., USD, EUR
 * @param string $host         Database host
 * @param string $user         Database user
 * @param string $pass         Database password
 * @param string $db           Database name
 */
class CurrencyRateTableGenerator {
    public function generateTable($currencyCode, $host, $user, $pass, $db) {
        $conn = new mysqli($host, $user, $pass, $db);

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