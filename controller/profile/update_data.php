<?php
session_start();

require_once __DIR__ . '/../../API/connection/koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php?page=login");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../index.php?page=akun");
    exit;
}

$id = $_POST['id'] ?? null;
$nama_UMKM = trim($_POST['nama_UMKM'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$errors = [];

if (empty($id) || $id != $_SESSION['user_id']) {
    $errors[] = "ID tidak valid";
}

if (empty($nama_UMKM)) {
    $errors[] = "Nama UMKM harus diisi";
}

if (empty($username)) {
    $errors[] = "Username harus diisi";
}

if (empty($email)) {
    $errors[] = "Email harus diisi";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid";
}

if (empty($no_hp)) {
    $errors[] = "No. Handphone harus diisi";
}

if (empty($alamat)) {
    $errors[] = "Alamat harus diisi";
}
if (!empty($new_password) || !empty($confirm_password)) {
    if ($new_password !== $confirm_password) {
        $errors[] = "Password baru dan konfirmasi password tidak cocok";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
}
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(', ', $errors);
    header("Location: ../../index.php?page=akun&status=error");
    exit;
}

try {
    $stmt = $database_connection->prepare("SELECT ID_user FROM users WHERE username = ? AND ID_user != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        $_SESSION['error_message'] = "Username sudah digunakan oleh user lain";
        header("Location: ../../index.php?page=akun&status=error");
        exit;
    }
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $database_connection->prepare("
            UPDATE users 
            SET nama_UMKM = ?, username = ?, email = ?, no_hp = ?, alamat = ?, password = ? 
            WHERE ID_user = ?
        ");
        $result = $stmt->execute([$nama_UMKM, $username, $email, $no_hp, $alamat, $hashed_password, $id]);
    } else {
        $stmt = $database_connection->prepare("
            UPDATE users 
            SET nama_UMKM = ?, username = ?, email = ?, no_hp = ?, alamat = ? 
            WHERE ID_user = ?
        ");
        $result = $stmt->execute([$nama_UMKM, $username, $email, $no_hp, $alamat, $id]);
    }

    if ($result) {
        $_SESSION['username'] = $username;
        $currentFoto = $_SESSION['user']['foto'] ?? null;
        
        $_SESSION['user'] = [
            'id' => $id,
            'nama_UMKM' => $nama_UMKM,
            'username' => $username,
            'email' => $email,
            'no_hp' => $no_hp,
            'alamat' => $alamat,
            'foto' => $currentFoto
        ];

        $_SESSION['success_message'] = "Profil berhasil diperbarui";
        header("Location: ../../index.php?page=akun&status=success");
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data ke database";
        header("Location: ../../index.php?page=akun&status=error");
        exit;
    }

} catch (Exception $e) {
    error_log("Error updating profile: " . $e->getMessage());
    $_SESSION['error_message'] = "Terjadi kesalahan sistem";
    header("Location: ../../index.php?page=akun&status=error");
    exit;
}
?>