<?php
// process_withdraw.php

// Include database connection
include "bd_conn.php";

// Check if data is received
if (isset($_POST['data'])) {
    // Decode received data
    $data = urldecode($_POST['data']);

    // Process the data as needed, e.g., update database stock

    // Example: Explode data to get T_ID and quantity
    $lines = explode("\n", $data);
    foreach ($lines as $line) {
        $parts = explode(" : ", $line);
        $t_id = $parts[0];
        $quantity = $parts[1];

        // Update database stock
        // Example SQL query: UPDATE cutting_tool SET stock = stock - $quantity WHERE T_ID = $t_id
        // Execute the query as needed
    }

    // Respond with success status
    echo "Success";
} else {
    // Respond with error status if no data received
    echo "Error: No data received";
}
?>