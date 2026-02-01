<?php
require_once "../../API/connection/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $database_connection->prepare("UPDATE produk SET is_deleted = 1 WHERE id_produk = ? AND ID_user = ?");
        $stmt->execute([$id, AUTH_ID]);

        echo "<script>alert('Produk berhasil dinonaktifkan!'); window.location.href='../../index.php?page=daftar-produk';</script>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}