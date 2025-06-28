<?php
include 'config/pdoconfig.php'; 

// Fetch total sales for all employees (cashiers)
try {
    $query = "SELECT 
                rpos_staff.staff_name AS cashier_name, 
                SUM(rpos_orders.total_amount) AS total_sales
              FROM rpos_orders
              INNER JOIN rpos_staff ON rpos_orders.staff_id = rpos_staff.id
              GROUP BY rpos_orders.staff_id";
    
    $stmt = $pdo->prepare($query); // Assuming $pdo is your PDO connection instance
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        echo "<h1>Cashier Sales</h1>";
        echo "<table border='1' cellpadding='10' cellspacing='0'>
                <tr>
                    <th>Cashier Name</th>
                    <th>Total Sales</th>
                </tr>";
        
        foreach ($results as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['cashier_name']) . "</td>
                    <td>$" . number_format($row['total_sales'], 2) . "</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<h1>No sales data available</h1>";
    }
} catch (Exception $e) {
    echo "Error fetching total sales: " . $e->getMessage();
}
?>
