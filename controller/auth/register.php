<?php
require_once __DIR__ . '/../../API/connection/cors.php';
require_once __DIR__ . '/../../API/connection/koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

$nama_UMKM = trim($data['nama_UMKM'] ?? '');
$username  = trim($data['username'] ?? '');
$email     = trim($data['email'] ?? '');
$password  = trim($data['password'] ?? '');
if (empty($nama_UMKM) || empty($username) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Semua data wajib diisi"]);
    exit;
}
$cek = $database_connection->prepare(
    "SELECT ID_user FROM users WHERE username=? OR email=?"
);
$cek->execute([$username, $email]);

if ($cek->rowCount() > 0) {
    echo json_encode(["success" => false, "message" => "Username atau Email sudah terdaftar"]);
    exit;
}
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);  
$stmt = $database_connection->prepare(
    "INSERT INTO users (nama_UMKM, username, email, password) 
     VALUES (?, ?, ?, ?)"
);

if ($stmt->execute([$nama_UMKM, $username, $email, $hashedPassword])) {
    echo json_encode([
        "success" => true, 
        "message" => "Registrasi berhasil! Silakan login."
    ]);
} else {
    echo json_encode([
        "success" => false, 
        "message" => "Terjadi kesalahan saat menyimpan data."
    ]);
}