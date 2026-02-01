<?php
require_once '../connection/koneksi.php';
// Ambil token dari cookie
$token = $_COOKIE['auth_token'] ?? '';

// Validasi token kosong
if ($token === '') {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "No auth cookie"
    ]);
    exit;
}

try {
    $sql = "SELECT * FROM users";
    $qconnect = $database_connection->prepare($sql);
    $qconnect->execute();
    $data = array();
    while ($row = $qconnect->fetch(PDO::FETCH_ASSOC)) {
        array_push($data, $row);
    }
    echo json_encode($data);
    
} catch (PDOException $err) {
    die("Tidak dapat memuat database $database_name: " . $err->getMessage());
}

?>