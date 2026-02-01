<?php

require_once __DIR__ . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLogin = false;

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    $isLogin = true;
} elseif (isset($_COOKIE['auth_token'])) {
    require_once BASE_PATH . '/controller/auth/auth_check.php';
}

if (!$isLogin) {
    require_once BASE_PATH . '/auth/loginregister.php';
    exit;
}

$page = $_GET['page'] ?? 'dashboard';

$titles = [
    'dashboard'          => 'Dashboard Overview',
    'daftar-produk'      => 'Daftar Produk',
    'input-produk'       => 'Input Produk Baru',
    'update-produk'      => 'Update Produk Baru',
    'produk-update'      => 'Update Produk ',
    'transaksi'          => 'Transaksi',
    'hitunghpp'          => 'Penghitungan HPP',
    'laporan-produk'     => 'Laporan Stok Produk',
    'laporan-transaksi'  => 'Laporan transaksi',
    'transaksi-detail'   => 'Laporan transaksi detail',
    'riwayat-hpp'        => 'Riwayat Hpp',
    'mutasi-produk'      => 'Mutasi Barang',
    'akun'               => 'Pengaturan Akun'
];
$currentTitle = $titles[$page] ?? 'Dashboard';

include BASE_PATH . '/layout/header.php';
include BASE_PATH . '/layout/sidebar.php';
?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #f5f5f7;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main-content {
    margin-left: 316px; 
    padding-top: 108px; 
    padding-right: 30px;
    padding-bottom: 30px;
    padding-left: 30px;
    flex: 1 0 auto;
    background-color: #f5f5f7;
    transition: margin-left 0.3s ease;
}

.container-fluid {
    max-width: 1400px;
    margin: auto;
}

.main-footer {
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding-top: 100px;
        padding: 20px;
    }
}
</style>

<div class="main-content">
    <div class="container-fluid">
        <?php
        switch ($page) {
            case 'dashboard':
                include __DIR__ . '/pages/dashboard.php';
                break;
            case 'daftar-produk':
                include __DIR__ . '/pages/produk/daftarproduk.php';
                break;
            case 'produk-update':
                include __DIR__ . '/pages/produk/updateproduk.php';
                break;
            case 'input-produk':
                include __DIR__ . '/pages/produk/inputproduk.php';
                break;
            case 'update-produk':
                include __DIR__ . '/pages/produk/updateproduk.php';
                break;
            case 'transaksi':
                include __DIR__ . '/pages/transaksi/transaksi.php';
                break;
            case 'hitunghpp':
                include __DIR__ . '/pages/hitung.php';
                break;
            case 'laporan-produk':
                include __DIR__ . '/pages/laporan/daftarproduk.php';
                break;
            case 'mutasi-produk':
                include __DIR__ . '/pages/laporan/MutasiProduk.php';
                break;
            case 'laporan-transaksi':
                include __DIR__ . '/pages/laporan/laporantransaksi.php';
                break;
            case 'transaksi-detail':
                include "pages/laporan/transaksidetail.php"; 
                break;
            case 'riwayat-hpp':
                include __DIR__ . '/pages/laporan/riwayat_hpp.php';
                break;
            case 'akun':
                include __DIR__ . '/pages/profile.php';
                break;
            case 'logout':
                include __DIR__ . '/controller/auth/logout.php';
                exit;
            default:
                include __DIR__ . '/pages/dashboard.php';
        }
        ?>
    </div>
</div>

<?php include BASE_PATH . '/layout/footer.php'; ?>

</body>
</html>