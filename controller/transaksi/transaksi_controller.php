<?php
date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/salsauas/API/connection/koneksi.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

if ($action == 'save') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!$data || empty($data['items'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
        exit;
    }

    try {
        $database_connection->beginTransaction();

        $invoice = "INV-" . date('YmdHis') . "-" . rand(10, 99);
        
        $total_bayar = 0;
        foreach ($data['items'] as $item) {
            $total_bayar += ($item['price'] * $item['qty']);
        }

        $sqlTx = "INSERT INTO transaksi (no_invoice, nama_customer, total_bayar, metode_pembayaran, tgl_transaksi, ID_user) 
                  VALUES (?, ?, ?, ?, NOW(), ?)"; 
        $stmtTx = $database_connection->prepare($sqlTx);
        $stmtTx->execute([
            $invoice, 
            $data['nama_customer'], 
            $total_bayar, 
            $data['metode_pembayaran'],
            $user_id
        ]);
        
        $id_transaksi = $database_connection->lastInsertId();

        $sqlDet = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal, tgl_detail) 
                   VALUES (?, ?, ?, ?, NOW())";
        $stmtDet = $database_connection->prepare($sqlDet);
        $sqlStok = "UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND ID_user = ?";
        $stmtStok = $database_connection->prepare($sqlStok);

        $sqlMutasi = "INSERT INTO mutasi_stok (id_produk, jenis_mutasi, jumlah, keterangan, ID_user) 
                      VALUES (?, 'Keluar', ?, ?, ?)";
        $stmtMutasi = $database_connection->prepare($sqlMutasi);

        foreach ($data['items'] as $item) {
            $subtotal = $item['price'] * $item['qty'];
            
            $stmtDet->execute([$id_transaksi, $item['id'], $item['qty'], $subtotal]);
            $stmtStok->execute([$item['qty'], $item['id'], $user_id]);

            $keterangan_mutasi = "Penjualan Invoice: " . $invoice;
            $stmtMutasi->execute([$item['id'], $item['qty'], $keterangan_mutasi, $user_id]);
        }

        $database_connection->commit();

        echo json_encode([
            'success' => true, 
            'invoice' => $invoice
        ]);

    } catch (Exception $e) {
        if ($database_connection->inTransaction()) {
            $database_connection->rollBack();
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}