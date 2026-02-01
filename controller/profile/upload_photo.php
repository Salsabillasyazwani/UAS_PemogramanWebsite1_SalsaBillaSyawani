<?php
session_start();

require_once __DIR__ . '/../../API/connection/koneksi.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Silakan login terlebih dahulu']);
    exit;
}
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
    exit;
}

$file = $_FILES['photo'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File terlalu besar (PHP ini)',
        UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (Form)',
        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ada',
        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
        UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension PHP'
    ];
    
    $message = $errorMessages[$file['error']] ?? 'Error tidak diketahui saat upload';
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$maxSize = 2 * 1024 * 1024; // 2MB
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar (max 2MB)']);
    exit;
}

// Validasi tipe file
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF']);
    exit;
}

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$newFileName = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
$uploadDir = __DIR__ . '/../../uploads/profile/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat folder upload. Periksa permission folder']);
        exit;
    }
}

if (!is_writable($uploadDir)) {
    echo json_encode(['success' => false, 'message' => 'Folder upload tidak bisa ditulis. Periksa permission (chmod 755 atau 777)']);
    exit;
}

$uploadPath = $uploadDir . $newFileName;

try {
    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode(['success' => false, 'message' => 'Gagal memindahkan file. Periksa permission folder uploads/profile/']);
        exit;
    }
    
    // Hapus foto lama jika ada
    if (!empty($_SESSION['user']['foto'])) {
        $oldPhoto = $uploadDir . $_SESSION['user']['foto'];
        if (file_exists($oldPhoto) && is_file($oldPhoto)) {
            @unlink($oldPhoto); 
        }
    }
    $userId = $_SESSION['user_id'];
    $stmt = $database_connection->prepare("UPDATE users SET foto = ? WHERE ID_user = ?");
    
    if ($stmt->execute([$newFileName, $userId])) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = [];
        }
        $_SESSION['user']['foto'] = $newFileName;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Foto berhasil diupload',
            'filename' => $newFileName,
            'path' => 'uploads/profile/' . $newFileName
        ]);
    } else {
        // Hapus file yang sudah diupload jika gagal update database
        @unlink($uploadPath);
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database']);
    }
    
} catch (Exception $e) {
    if (file_exists($uploadPath)) {
        @unlink($uploadPath);
    }
    
    error_log("Error uploading photo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
}

exit;
?>