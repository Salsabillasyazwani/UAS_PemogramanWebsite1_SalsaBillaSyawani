<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/salsauas/API/connection/koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: index.php?page=daftar-produk');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $database_connection->prepare("SELECT * FROM produk WHERE id_produk = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produk) {
    header('Location: index.php?page=daftar-produk');
    exit;
}
?>

<style>
    .form-container {
        background: #ffffff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .form-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eeeeee;
    }

    .form-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #4b5563;
    }
    .form-group input, 
    .form-group select {
        padding: 12px 16px;
        font-size: 14px;
        background-color: #f9fafb;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s ease-in-out;
    }

    .form-group input:focus, 
    .form-group select:focus {
        background-color: #ffffff;
        border-color: #880d0d;
        box-shadow: 0 0 0 4px rgba(136, 13, 13, 0.1);
    }
    .image-preview-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 5px;
    }

    .img-preview {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        border: 1px solid #dddddd;
        object-fit: cover;
    }

    .no-img-box {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eeeeee;
        border-radius: 10px;
        color: #999999;
        font-size: 12px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
        padding-top: 20px;
        border-top: 1px solid #eeeeee;
    }

    .btn {
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .btn:active {
        transform: scale(0.98);
    }

    .btn-submit {
        background-color: #880d0d;
        color: #ffffff;
    }

    .btn-reset {
        background-color: #f3f4f6;
        color: #4b5563;
    }
    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2>
                <i class="fas fa-edit" style="margin-right:10px; color:#880d0d;"></i>
                Update Produk
            </h2>
        </div>

        <form id="productForm" action="controller/produk/update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">

            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori Produk</label>
                    <select name="id_kategori" required>
                        <option value="1" <?= $produk['id_kategori'] == 1 ? 'selected' : '' ?>>Makanan</option>
                        <option value="2" <?= $produk['id_kategori'] == 2 ? 'selected' : '' ?>>Snack</option>
                        <option value="3" <?= $produk['id_kategori'] == 3 ? 'selected' : '' ?>>Minuman</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Harga Satuan (Rp)</label>
                    <input type="number" name="harga_satuan" value="<?= $produk['harga_satuan'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= $produk['stok'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Ganti Gambar (Opsional)</label>
                    <input type="file" name="gambar" accept="image/*" onchange="previewUpdate(event)">
                    <div class="image-preview-wrapper">
                        <?php if (!empty($produk['gambar'])): ?>
                           <img id="currImg" src="assets/images/produk/<?= $produk['gambar'] ?>" class="img-preview">
                        <?php else: ?>
                            <div id="noImg" class="no-img-box">No Image</div>
                        <?php endif; ?>
                        <i class="fas fa-arrow-right" style="color:#ccc; display:none;" id="arrow"></i>
                        <img id="newImgPreview" class="img-preview" style="display:none;">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Produksi (MFG)</label>
                    <input type="date" name="mfg_date" value="<?= $produk['tgl_produksi'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Kadaluarsa (EXP)</label>
                    <input type="date" name="exp_date" value="<?= $produk['tgl_kadaluarsa'] ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn btn-reset">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save"></i> Update Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewUpdate(event) {
        const file = event.target.files[0];
        const newImg = document.getElementById('newImgPreview');
        const arrow = document.getElementById('arrow');

        if (file) {
            if (!file.type.match('image.*')) {
                alert("Mohon pilih file gambar (JPG, PNG, atau WEBP)!");
                event.target.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert("Ukuran gambar terlalu besar! Maksimal 2MB.");
                event.target.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function() {
                if (reader.readyState === 2) {
                    newImg.src = reader.result;
                    newImg.style.display = 'block';
                    arrow.style.display = 'block';
                }
            }
            reader.readAsDataURL(file);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('productForm');
        form.addEventListener('reset', function (e) {
            e.preventDefault();
            window.location.href = 'index.php?page=daftar-produk';
        });
        form.addEventListener('submit', function(e) {
            const mfgDate = new Date(document.getElementsByName('mfg_date')[0].value);
            const expDate = new Date(document.getElementsByName('exp_date')[0].value);
            if (expDate <= mfgDate) {
                alert("GAGAL: Tanggal kadaluarsa harus lebih besar dari tanggal produksi!");
                e.preventDefault();
            }
        });
    });
</script>