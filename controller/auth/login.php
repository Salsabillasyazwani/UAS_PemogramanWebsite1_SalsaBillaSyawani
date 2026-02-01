<?php
session_start();

require_once __DIR__ . '/../../API/connection/koneksi.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$user = trim($data['username'] ?? '');
$pass = trim($data['password'] ?? '');

// Validasi input
if (empty($user) || empty($pass)) {
    echo json_encode(["success" => false, "message" => "Username dan password harus diisi"]);
    exit;
}

try {
    $stmt = $database_connection->prepare("
        SELECT 
            ID_user,
            nama_UMKM,
            username,
            email,
            no_hp,
            alamat,
            foto,
            password
        FROM users 
        WHERE username = ?
        LIMIT 1
    ");
    
    $stmt->execute([$user]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek user dan password
    if (!$row || !password_verify($pass, $row['password'])) {
        echo json_encode(["success" => false, "message" => "Username atau password salah"]);
        exit;
    }

    // Generate token untuk cookie
    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);

    // Update token di database
    $database_connection->prepare("UPDATE users SET token = ? WHERE ID_user = ?")
        ->execute([$hash, $row['ID_user']]);

    // Set cookie
    $expired_time = time() + (86400 * 7); 
    setcookie("auth_token", $token, [
        "expires" => $expired_time,
        "path" => "/",
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    $_SESSION['user_id'] = $row['ID_user'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user'] = [
        'id' => $row['ID_user'],
        'nama_UMKM' => $row['nama_UMKM'],
        'username' => $row['username'],
        'email' => $row['email'],
        'no_hp' => $row['no_hp'],
        'alamat' => $row['alamat'],
        'foto' => $row['foto']
    ];

    echo json_encode([
        "success" => true, 
        "message" => "Login berhasil",
        "user" => [
            "username" => $row['username'],
            "nama_UMKM" => $row['nama_UMKM']
        ]
    ]);

} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "message" => "Terjadi kesalahan sistem"
    ]);
}
?>