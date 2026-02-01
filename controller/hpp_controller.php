<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/../API/connection/koneksi.php';

header('Content-Type: application/json');
$user_id = $_SESSION['user_id'] ?? 0;

$action = $_GET['action'] ?? '';

if ($action == 'save') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!$data || empty($data['nama_produk'])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
        exit;
    }

    try {
        $database_connection->beginTransaction();

        $qty         = (float)($data['jumlah_produksi'] ?: 1);
        $bahan       = (float)($data['bahan_baku'] ?: 0);
        $tenaga      = (float)($data['tenaga_kerja'] ?: 0);
        $overhead    = (float)($data['overhead'] ?: 0);
        $tak_terduga = (float)($data['biaya_tak_terduga'] ?: 0);
        $margin      = (float)($data['margin'] ?: 0);
        $pajak_input = (float)($data['pajak'] ?: 0);

        $total_hpp      = $bahan + $tenaga + $overhead + $tak_terduga;
        $hpp_per_unit   = $total_hpp / $qty;
        $harga_jual     = $hpp_per_unit + $margin;
        $harga_jual_ppn = $harga_jual + $pajak_input;
        $keuntungan     = $margin - $pajak_input;

        $sql = "INSERT INTO hpp (
                    nama_produk, jumlah_produksi, bahan_baku, tenaga_kerja, 
                    overhead, biaya_tak_terduga, total_hpp, hpp_per_unit, 
                    margin, harga_jual, pajak, harga_jual_pajak, keuntungan_bersih, ID_user
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $database_connection->prepare($sql);
        $stmt->execute([
            $data['nama_produk'],
            $qty,
            $bahan,
            $tenaga,
            $overhead,
            $tak_terduga,
            $total_hpp,
            $hpp_per_unit,
            $margin,
            $harga_jual,
            $pajak_input,
            $harga_jual_ppn,
            $keuntungan,
            $user_id 
        ]);

        $database_connection->commit();

        echo json_encode([
            'success' => true, 
            'message' => 'Data berhasil disimpan'
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