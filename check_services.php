<?php
require_once __DIR__ . '/kon/conn.php';

try {
    $stmt = $conn->query("SELECT * FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($services)) {
        echo "Table 'services' exists but is EMPTY.\n";
    } else {
        echo "Table 'services' contains " . count($services) . " rows.\n";
        print_r($services);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
