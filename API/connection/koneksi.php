<?php
require_once __DIR__ . '/cors.php';

$database_hostname = "localhost";
$database_username = "aspw3767_salsauas";
$database_password = "@salsa310505";
$database_name     = "aspw3767_salsauas";
$database_port     = "3306";

try {
    $database_connection = new PDO(
        "mysql:host=$database_hostname;port=$database_port;dbname=$database_name;charset=utf8",
        $database_username,
        $database_password
      
    );
    $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Koneksi database gagal"
    ]);
    exit;
}