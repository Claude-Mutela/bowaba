<?php
require_once __DIR__ . '/kon/conn.php';

try {
    echo "<h1>Table Schema: services</h1>";
    $stmt = $conn->query("DESCRIBE services");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($columns);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
