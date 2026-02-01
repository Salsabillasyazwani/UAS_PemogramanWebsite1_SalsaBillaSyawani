<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../API/connection/koneksi.php';

$user_id = $_SESSION['user_id'] ?? 0;

try {
    $stmt = $database_connection->prepare(
        "SELECT p.*, k.nama_kategori 
         FROM produk p 
         LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
         WHERE p.is_deleted = 0 AND p.ID_user = ? 
         ORDER BY p.id_produk DESC"
    );
    $stmt->execute([$user_id]);
    $produk = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $stmt_stats = $database_connection->prepare(
        "SELECT COUNT(*) as total_item, SUM(stok) as total_stok FROM produk WHERE is_deleted = 0 AND ID_user = ?"
    );
    $stmt_stats->execute([$user_id]);
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    $totalProduk = $stats['total_item'] ?? 0;
    $totalStock  = $stats['total_stok'] ?? 0;

} catch (PDOException $e) {
    $produk = [];
    $totalProduk = 0;
    $totalStock = 0;
}