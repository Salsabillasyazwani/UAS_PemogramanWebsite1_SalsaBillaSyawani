<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../API/connection/koneksi.php";
$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk    = $_POST['id_produk'];
    $nama_produk  = $_POST['nama_produk'];
    $id_kategori  = $_POST['id_kategori'];
    $harga_satuan = $_POST['harga_satuan'];
    $stok_baru    = $_POST['stok'];
    $mfg_date     = $_POST['mfg_date'];
    $exp_date     = $_POST['exp_date'];

    try {
        $stmt_cek = $database_connection->prepare("SELECT gambar, stok FROM produk WHERE id_produk = ? AND ID_user = ?");
        $stmt_cek->execute([$id_produk, $user_id]);
        $data_lama = $stmt_cek->fetch(PDO::FETCH_ASSOC);
        
        if (!$data_lama) {
            die("Akses ditolak atau produk tidak ditemukan.");
        }

        $nama_file_final = $data_lama['gambar'];
        $stok_lama = $data_lama['stok'];
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            if (!empty($data_lama['gambar'])) {
                $path_lama = '../../assets/images/produk/' . $data_lama['gambar'];
                if (file_exists($path_lama)) unlink($path_lama);
            }
            $nama_file_final = "prod_" . uniqid() . "." . strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../../assets/images/produk/' . $nama_file_final);
        }

        $database_connection->beginTransaction();
        $sql = "UPDATE produk SET nama_produk=:n, id_kategori=:k, harga_satuan=:h, stok=:s, tgl_produksi=:m, tgl_kadaluarsa=:e, gambar=:g 
                WHERE id_produk=:id AND ID_user=:user_id";
        $stmt = $database_connection->prepare($sql);
        $stmt->execute([
            ':n'=>$nama_produk, ':k'=>$id_kategori, ':h'=>$harga_satuan, ':s'=>$stok_baru, 
            ':m'=>$mfg_date, ':e'=>$exp_date, ':g'=>$nama_file_final, ':id'=>$id_produk, ':user_id'=>$user_id
        ]);
        if ($stok_baru != $stok_lama) {
            $selisih = $stok_baru - $stok_lama;
            $jenis_mutasi = ($selisih > 0) ? 'Masuk' : 'Keluar';
            $jumlah_final = abs($selisih);
            $ket = "Perubahan stok manual (Dari $stok_lama ke $stok_baru)";

            $sql_mutasi = "INSERT INTO mutasi_stok (id_produk, jenis_mutasi, jumlah, keterangan, ID_user) VALUES (?, ?, ?, ?, ?)";
            $stmt_mutasi = $database_connection->prepare($sql_mutasi);
            $stmt_mutasi->execute([$id_produk, $jenis_mutasi, $jumlah_final, $ket, $user_id]);
        }

        $database_connection->commit();
        echo "<script>alert('Produk Berhasil Diperbarui!'); window.location.href='../../index.php?page=daftar-produk';</script>";

    } catch (PDOException $e) {
        if ($database_connection->inTransaction()) $database_connection->rollBack();
        die("Error: " . $e->getMessage());
    }
}