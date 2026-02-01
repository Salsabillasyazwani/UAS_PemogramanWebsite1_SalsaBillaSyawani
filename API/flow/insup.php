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
// Ambil data POST
$namaUMKM = trim($_POST['nama_UMKM'] ?? '');
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi
if ($namaUMKM === '' || $username === '' || $email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "message" => "Semua field wajib diisi"
    ]);
    exit;
}

// Cek user sudah ada
$cek = $database_connection->prepare(
    "SELECT ID_user FROM users WHERE username=? OR email=?"
);
$cek->execute([$username, $email]);

if ($cek->rowCount() > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Username atau email sudah terdaftar"
    ]);
    exit;
}

// INSERT DATA
$stmt = $database_connection->prepare(
    "INSERT INTO users (nama_UMKM, username, email, password)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([
    $namaUMKM,
    $username,
    $email,
    sha1($password)
]);

echo json_encode([
    "success" => true,
    "message" => "Register berhasil"
]);
