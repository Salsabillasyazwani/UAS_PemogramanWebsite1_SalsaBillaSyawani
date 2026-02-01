<?php
require_once '../auth/auth_check.php';
require_once '../connection/koneksi.php';

$id = $_GET['id'] ?? 0;

$stmt = $database_connection->prepare(
    "DELETE FROM users WHERE ID_user=?"
);
$stmt->execute([$id]);

echo json_encode(["success"=>true]);
