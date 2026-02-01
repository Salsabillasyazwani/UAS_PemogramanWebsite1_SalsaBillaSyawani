<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../API/connection/koneksi.php";

$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk   = $_POST['nama_produk'];
    $id_kategori   = $_POST['id_kategori'];
    $harga_satuan  = $_POST['harga_satuan'];
    $stok          = $_POST['stok'];
    $mfg_date      = $_POST['mfg_date'];
    $exp_date      = $_POST['exp_date'];

    $nama_file_baru = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $ekstensi_file  = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $nama_file_baru = "prod_" . uniqid() . "." . $ekstensi_file;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../../assets/images/produk/' . $nama_file_baru);
    }

    try {
        $database_connection->beginTransaction();

        $query = "INSERT INTO produk (nama_produk, id_kategori, harga_satuan, stok, tgl_produksi, tgl_kadaluarsa, gambar, ID_user) 
                  VALUES (:nama, :id_kat, :harga, :stok, :mfg, :exp, :img, :user_id)";
        $stmt = $database_connection->prepare($query);
        $stmt->execute([
            ':nama'   => $nama_produk, 
            ':id_kat' => $id_kategori, 
            ':harga'  => $harga_satuan,
            ':stok'   => $stok, 
            ':mfg'    => $mfg_date, 
            ':exp'    => $exp_date, 
            ':img'    => $nama_file_baru,
            ':user_id'=> $user_id
        ]);

        $id_produk_baru = $database_connection->lastInsertId();

        $query_mutasi = "INSERT INTO mutasi_stok (id_produk, jenis_mutasi, jumlah, keterangan, ID_user) 
                         VALUES (?, 'Masuk', ?, 'Input Stok Awal Produk Baru', ?)";
        $stmt_mutasi = $database_connection->prepare($query_mutasi);
        $stmt_mutasi->execute([$id_produk_baru, $stok, $user_id]);

        $database_connection->commit();

        echo "<script>alert('Produk Berhasil Ditambah!'); window.location.href='../../index.php?page=daftar-produk';</script>";
    } catch (PDOException $e) {
        if ($database_connection->inTransaction()) {
            $database_connection->rollBack();
        }
        die("Error: " . $e->getMessage());
    }
}