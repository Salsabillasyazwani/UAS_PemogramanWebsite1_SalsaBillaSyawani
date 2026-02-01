<?php
?>

<style>
     body {
            background-color: #f4f4f4;
            color: var(--text);
            height: 100vh;
            overflow: auto;
            padding: 10px;
        }
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        height : 100vh;
    }

    .form-header {
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        
    }

    .form-header h2 {
        font-size: 20px;
        color: #1a1a1a;
        font-weight: 700;
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

    .form-group input, .form-group select {
        padding: 12px 16px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        outline: none;
        background-color: #f9fafb;
    }

    .form-group input:focus, .form-group select:focus {
        border-color: #880d0d;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(136, 13, 13, 0.1);
    }

    .image-preview {
        width: 100%;
        max-width: 150px;
        height: 150px;
        border: 2px dashed #e5e7eb;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 10px;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        font-size: 14px;
    }

    .btn-submit {
        background-color: #880d0d;
        color: white;
    }

    .btn-submit:hover {
        background-color: #ff1313;
        transform: translateY(-2px);
    }

    .btn-reset {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    @media (max-width: 480px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle" style="margin-right: 10px; color: #880d0d;"></i> Tambah Produk Baru</h2>
        </div>

        <form id="productForm" action="controller/produk/save.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="nama_produk">Nama Produk</label>
                    <input type="text" id="nama_produk" name="nama_produk" placeholder="Contoh: Susu UHT 250ml" required>
                </div>
                <div class="form-group">
                    <label for="id_kategori">Kategori Produk</label>
                    <select id="id_kategori" name="id_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="1">Makanan</option>
                        <option value="2">Snack</option>
                        <option value="3">Minuman</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="harga_satuan">Harga Satuan (Rp)</label>
                    <input type="number" id="harga_satuan" name="harga_satuan" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label for="stok">Jumlah Stok</label>
                    <input type="number" id="stok" name="stok" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar Produk</label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
                    <div class="image-preview" id="imgPreviewContainer">
                        <i class="fas fa-image" id="placeholderIcon" style="font-size: 30px; color: #ccc;"></i>
                        <img id="imgPreview" src="#" alt="Preview">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mfg_date">Tanggal Produksi (MFG)</label>
                    <input type="date" id="mfg_date" name="mfg_date" required>
                </div>
                <div class="form-group">
                    <label for="exp_date">Tanggal Kadaluarsa (EXP)</label>
                    <input type="date" id="exp_date" name="exp_date" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn btn-reset" onclick="resetPreview()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const imgPreview = document.getElementById('imgPreview');
    const icon = document.getElementById('placeholderIcon');

    if (file) {
        if (!file.type.match('image.*')) {
            alert("Harus berupa file gambar (JPG, PNG, atau WEBP)!");
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
            imgPreview.src = reader.result;
            imgPreview.style.display = 'block';
            icon.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

function resetPreview() {
    const imgPreview = document.getElementById('imgPreview');
    const icon = document.getElementById('placeholderIcon');
    
    imgPreview.src = '#';
    imgPreview.style.display = 'none';
    icon.style.display = 'block';
    
    setTimeout(() => {
        document.getElementById('nama_produk').focus();
    }, 100);
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        alert("Produk Berhasil Disimpan!");
        const newUrl = window.location.pathname + '?page=input-produk';
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>