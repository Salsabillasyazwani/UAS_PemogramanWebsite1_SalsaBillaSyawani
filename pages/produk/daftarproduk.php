<?php
require_once __DIR__ . '/../../controller/produk/daftar.php';
?>

<style>
    .product-list-wrapper {
        width: 100%;
        height: calc(100vh - 140px);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .product-list-container {
        flex: 1;
        background: #ffffff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow-y: auto;
        min-height: 0;
    }
    .stats-bar {
        display: flex;
        gap: 20px;
        padding: 15px 25px;
        background: #880d0d;
        border-radius: 12px;
        margin-bottom: 20px;
        color: #ffffff;
    }

    .stat-item {
        font-size: 14px;
        font-weight: 500;
    }

    .stat-item strong {
        font-size: 16px;
        margin-left: 5px;
    }

    .product-card {
        display: grid;
        grid-template-columns: 100px 1fr auto;
        gap: 20px;
        align-items: center;
        padding: 18px;
        border: 1.5px solid #f1f5f9;
        border-radius: 14px;
        margin-bottom: 15px;
        transition: all 0.2s ease-in-out;
    }

    .product-card:hover {
        border-color: #880d0d;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .product-img-container {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .product-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-img-container .no-img {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #cbd5e1;
        font-size: 28px;
    }

    .product-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .category-badge {
        display: inline-block;
        width: fit-content;
        padding: 4px 12px;
        background: #fff1f1;
        color: #880d0d;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .product-name {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .product-price {
        font-size: 16px;
        font-weight: 700;
        color: #880d0d;
        margin: 0;
    }

    .product-meta {
        display: flex;
        gap: 15px;
        margin-top: 8px;
    }

    .meta-item {
        font-size: 12px;
        color: #64748b;
    }

    .product-actions {
        display: flex;
        gap: 10px;
    }

    .btn-action {
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        border: none;
    }

    .btn-update {
        background: #880d0d;
        color: #ffffff;
    }

    .btn-update:hover {
        background: #ff1313;
    }

    .btn-delete {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-delete:hover {
        background: #e2e8f0;
        color: #ef4444;
    }

  
    @media (max-width: 768px) {
        .product-card {
            grid-template-columns: 80px 1fr;
        }
        .product-actions {
            grid-column: span 2;
            justify-content: flex-end;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }
    }
</style>

<div class="product-list-wrapper">
    <div class="stats-bar">
        <div class="stat-item">Total Produk: <strong id="totalProducts"><?= $totalProduk ?></strong></div>
        <div class="stat-item">Total Stock: <strong id="totalStock"><?= number_format($totalStock, 0, ',', '.') ?></strong></div>
    </div>

    <div class="product-list-container">
        <div id="productList">
            <?php if (!empty($produk)): ?>
                <?php foreach ($produk as $row): ?>
                    <div class="product-card" data-id="<?= $row['id_produk'] ?>">
                        <div class="product-img-container">
                            <?php 
                            $imgPath = 'assets/images/produk/' . $row['gambar'];
                            if (!empty($row['gambar']) && file_exists($imgPath)): ?>
                                <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                            <?php else: ?>
                                <div class="no-img"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <span class="category-badge">
                                <?= htmlspecialchars($row['nama_kategori'] ?? 'Umum') ?>
                            </span>
                            <h3 class="product-name"><?= htmlspecialchars($row['nama_produk']) ?></h3>
                            <p class="product-price">Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></p>

                            <div class="product-meta">
                                <span class="meta-item"><strong>Stok:</strong> <?= $row['stok'] ?></span>
                                <span class="meta-item">
                                    <strong>EXP:</strong> 
                                    <?= date('d/m/Y', strtotime($row['tgl_kadaluarsa'])) ?>
                                </span>
                            </div>
                        </div>

                        <div class="product-actions">
                            <button class="btn-action btn-update" onclick="updateProduct(<?= $row['id_produk'] ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteProduct(<?= $row['id_produk'] ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 10px;"></i>
                    <p>Belum ada data produk.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Fungsi Redirect ke Halaman Edit
function updateProduct(id) {
    window.location.href = '?page=update-produk&id=' + id;
}

// Fungsi Delete dengan Konfirmasi
function deleteProduct(id) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini? Gambar produk juga akan terhapus.')) {
        window.location.href = 'controller/produk/delete.php?id=' + id;
    }
}

// Menangani Notifikasi Success
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('updated') === '1') {
        alert("Data produk berhasil diperbarui!");
        const newUrl = window.location.pathname + '?page=daftar-produk';
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>