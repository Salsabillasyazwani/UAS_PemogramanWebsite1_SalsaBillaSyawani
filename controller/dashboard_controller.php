<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/salsauas/API/connection/koneksi.php';

$user_id = $_SESSION['user_id'] ?? 0;

try {
    $sqlStats = "SELECT 
        IFNULL((SELECT SUM(dt.jumlah) FROM detail_transaksi dt 
                JOIN transaksi t ON dt.id_transaksi = t.id_transaksi 
                WHERE DATE(dt.tgl_detail) = CURDATE() AND t.ID_user = :uid), 0) as terjual_hari,
        IFNULL((SELECT SUM(dt.jumlah) FROM detail_transaksi dt 
                JOIN transaksi t ON dt.id_transaksi = t.id_transaksi 
                WHERE MONTH(dt.tgl_detail) = MONTH(CURDATE()) AND t.ID_user = :uid), 0) as terjual_bulan,
        IFNULL((SELECT SUM(dt.subtotal) FROM detail_transaksi dt 
                JOIN transaksi t ON dt.id_transaksi = t.id_transaksi 
                WHERE DATE(dt.tgl_detail) = CURDATE() AND t.ID_user = :uid), 0) as omzet_hari,
        IFNULL((SELECT SUM(dt.subtotal) FROM detail_transaksi dt 
                JOIN transaksi t ON dt.id_transaksi = t.id_transaksi 
                WHERE MONTH(dt.tgl_detail) = MONTH(CURDATE()) AND t.ID_user = :uid), 0) as omzet_bulan";
    
    $stmtStats = $database_connection->prepare($sqlStats);
    $stmtStats->execute(['uid' => $user_id]);
    $queryStats = $stmtStats->fetch(PDO::FETCH_ASSOC);

    $sqlGrafik = "SELECT dt.tgl_detail as tanggal, SUM(dt.jumlah) as qty, SUM(dt.subtotal) as omzet 
                  FROM detail_transaksi dt
                  JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                  WHERE dt.tgl_detail >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND t.ID_user = :uid
                  GROUP BY dt.tgl_detail ORDER BY dt.tgl_detail ASC";
    
    $stmtGrafik = $database_connection->prepare($sqlGrafik);
    $stmtGrafik->execute(['uid' => $user_id]);
    $resGrafik = $stmtGrafik->fetchAll(PDO::FETCH_ASSOC);

    $grafikData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $grafikData[$date] = [
            'tanggal' => $date,
            'qty' => 0,
            'omzet' => 0,
            'label' => date('d M', strtotime($date))
        ];
    }

    foreach ($resGrafik as $data) {
        if (isset($grafikData[$data['tanggal']])) {
            $grafikData[$data['tanggal']]['qty'] = (int)$data['qty'];
            $grafikData[$data['tanggal']]['omzet'] = (float)$data['omzet'];
        }
    }
    $grafikData = array_values($grafikData);

    $max_qty = 1;
    $max_omzet = 1;
    foreach($grafikData as $d) {
        if($d['qty'] > $max_qty) $max_qty = $d['qty'];
        if($d['omzet'] > $max_omzet) $max_omzet = $d['omzet'];
    }

    $points_qty = "";
    $points_omzet = "";
    $labels_grafik = [];
    $chartHeight = 120;
    $chartWidth = 280;
    $padding = 20;

    foreach ($grafikData as $index => $data) {
        $x = ($index / 6) * $chartWidth + $padding;
        $y_qty = $chartHeight - (($data['qty'] / $max_qty) * $chartHeight) + $padding;
        $y_omzet = $chartHeight - (($data['omzet'] / $max_omzet) * $chartHeight) + $padding;
        $points_qty .= "$x,$y_qty ";
        $points_omzet .= "$x,$y_omzet ";
        $labels_grafik[] = [
            'x' => $x,
            'txt' => $data['label'],
            'qty_val' => $data['qty'],
            'omzet_val' => number_format($data['omzet']/1000, 0) . 'k'
        ];
    }

    $sqlTerlaris = "SELECT p.*, SUM(dt.jumlah) as terjual 
                    FROM produk p 
                    JOIN detail_transaksi dt ON p.id_produk = dt.id_produk 
                    JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                    WHERE t.ID_user = :uid
                    GROUP BY p.id_produk ORDER BY terjual DESC LIMIT 10";
    
    $stmtTerlaris = $database_connection->prepare($sqlTerlaris);
    $stmtTerlaris->execute(['uid' => $user_id]);
    $produk_terlaris = $stmtTerlaris->fetchAll(PDO::FETCH_ASSOC);

    $yaxis_labels = [
        'qty' => [
            ['val' => 0, 'y' => $chartHeight + $padding],
            ['val' => round($max_qty/2), 'y' => ($chartHeight/2) + $padding],
            ['val' => $max_qty, 'y' => $padding]
        ],
        'omzet' => [
            ['val' => '0', 'y' => $chartHeight + $padding],
            ['val' => number_format($max_omzet/2000, 0) . 'k', 'y' => ($chartHeight/2) + $padding],
            ['val' => number_format($max_omzet/1000, 0) . 'k', 'y' => $padding]
        ]
    ];

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
}
?>