<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/salsauas/API/connection/koneksi.php';

$user_id = $_SESSION['user_id'] ?? 0;

try {
    $query = "SELECT p.*, k.nama_kategori 
              FROM produk p 
              LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
              WHERE p.stok > 0 AND p.ID_user = ? AND p.is_deleted = 0";
    
    $stmt = $database_connection->prepare($query);
    $stmt->execute([$user_id]);
    $produk_db = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $stmt_cat = $database_connection->prepare("SELECT * FROM kategori");
    $stmt_cat->execute();
    $daftar_kategori = $stmt_cat->fetchAll(PDO::FETCH_ASSOC) ?: [];

} catch (PDOException $e) {
    $produk_db = [];
    $daftar_kategori = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-Manage POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --m: #880d0d;
            --m-light: #fff5f5;
            --text: #333;
            --border: #e0e0e0;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            color: var(--text);
            height: 100vh;
            overflow: hidden;
            padding: 10px;
        }

        .app-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 15px;
            height: 100%;
        }

        .catalog-container {
            background: var(--white);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .cat-header {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 8px;
            overflow-x: auto;
            background: #fafafa;
        }

        .cat-btn {
            padding: 7px 15px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--white);
            cursor: pointer;
            white-space: nowrap;
            font-size: 13px;
            transition: 0.2s;
        }

        .cat-btn.active {
            background: var(--m);
            color: var(--white);
            border-color: var(--m);
        }

        .product-grid {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 15px;
            align-content: start;
        }

        .p-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            background: var(--white);
            transition: 0.2s;
        }

        .p-img {
            width: 100%;
            height: 90px;
            border-radius: 9px;
            object-fit: cover;
            margin-bottom: 8px;
            background: #f0f0f0;
        }

        .p-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
            height: 36px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .p-price {
            font-size: 14px;
            color: var(--m);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .billing-container {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .bill-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
            font-weight: bold;
            font-size: 16px;
            background: #fafafa;
        }

        .cart-list {
            flex: 1;
            overflow-y: auto;
            padding: 0 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f8f8f8;
        }

        .cart-ctrl {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }

        .btn-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid var(--m);
            background: white;
            color: var(--m);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .qty-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--m);
            border-radius: 6px;
            height: 32px;
            overflow: hidden;
        }

        .qty-btn {
            background: none;
            border: none;
            color: white;
            width: 30px;
            height: 100%;
            cursor: pointer;
        }

        .qty-num {
            flex: 1;
            color: white;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
        }

        .method-select {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .m-pill {
            flex: 1;
            padding: 8px;
            text-align: center;
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.2s;
        }

        .m-pill.active {
            border-color: var(--m);
            color: var(--m);
            background: var(--m-light);
            font-weight: bold;
        }

        .btn-checkout {
            width: 100%;
            padding: 14px;
            background: var(--m);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        #modalStruk {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            width: 380px;
            padding: 20px;
            border-radius: 12px;
        }

        .struk-card {
            font-family: 'Courier New', Courier, monospace;
            color: #333;
            padding: 10px;
        }

        .struk-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .struk-divider {
            border-top: 1px dashed #bbb;
            margin: 10px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 5px;
        }

        @media print {
            body * { visibility: hidden; }
            #strukArea, #strukArea * { visibility: visible; }
            #strukArea { position: fixed; left: 0; top: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="app-container">
    <div class="catalog-container">
        <div class="cat-header">
            <button class="cat-btn active" onclick="filterCat('all', this)">Semua</button>
            <?php foreach($daftar_kategori as $kat): ?>
                <button class="cat-btn" onclick="filterCat('<?= strtolower($kat['nama_kategori']) ?>', this)">
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div id="productGrid" class="product-grid"></div>
    </div>

    <div class="billing-container">
        <div class="bill-header">Detail Pesanan</div>
        <div class="cust-field" style="padding: 15px 20px;">
            <input type="text" id="custName" class="input-minimal" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; outline:none;" placeholder="Nama Pelanggan...">
        </div>
        <div id="cartList" class="cart-list"></div>
        <div class="bill-footer" style="padding:20px; border-top:1px solid #eee;">
            <div class="method-select">
                <div class="m-pill active" onclick="setMethod(this)">Cash</div>
                <div class="m-pill" onclick="setMethod(this)">QRIS</div>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-weight:800; font-size:18px; color:var(--m);">
                <span>Total</span>
                <span id="totalPrice">Rp 0</span>
            </div>
            <button class="btn-checkout" onclick="prosesBayar()">Bayar Sekarang</button>
        </div>
    </div>
</div>

<div id="modalStruk">
    <div class="modal-content">
        <div id="strukArea"></div>
        <div style="display:flex; flex-direction: column; gap:10px; margin-top:20px;">
            <div style="display:flex; gap:10px;">
                <button onclick="window.print()" style="flex:1; padding:12px; background:var(--m); color:white; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Cetak</button>
                <button onclick="downloadStruk()" style="flex:1; padding:12px; background:#27ae60; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">Simpan PDF</button>
            </div>
            <button onclick="location.reload()" style="padding:10px; background:#eee; border:none; border-radius:6px; cursor:pointer;">Tutup</button>
        </div>
    </div>
</div>

<script>
    const products = <?= json_encode(array_map(function($p) {
        return [
            'id' => (int)$p['id_produk'],
            'name' => $p['nama_produk'],
            'cat' => strtolower($p['nama_kategori'] ?? 'umum'),
            'price' => (int)$p['harga_satuan'],
            'stock' => (int)$p['stok'],
            'img' => $p['gambar']
        ];
    }, $produk_db)); ?>;

    let cart = [];
    let currentInvoice = "";

    function renderGrid(filter = 'all') {
        const grid = document.getElementById('productGrid');
        const list = filter === 'all' ? products : products.filter(p => p.cat === filter);
        grid.innerHTML = list.map(p => {
            const inCart = cart.find(x => x.id === p.id);
            const imgPath = p.img ? `assets/images/produk/${p.img}` : 'https://via.placeholder.com/150';
            return `
            <div class="p-card">
                <img src="${imgPath}" class="p-img">
                <div class="p-name">${p.name}</div>
                <div class="p-price">Rp ${p.price.toLocaleString('id-ID')}</div>
                ${inCart ? `
                    <div class="qty-wrapper">
                        <button class="qty-btn" onclick="updateQty(${p.id}, -1)">-</button>
                        <div class="qty-num">${inCart.qty}</div>
                        <button class="qty-btn" onclick="updateQty(${p.id}, 1)">+</button>
                    </div>
                ` : `<button class="add-btn" onclick="updateQty(${p.id}, 1)" style="width:100%; padding:7px; background:var(--m); color:white; border:none; border-radius:6px; cursor:pointer;">Tambah</button>`}
            </div>`;
        }).join('');
    }

    function updateQty(id, n) {
        const p = products.find(x => x.id === id);
        const item = cart.find(x => x.id === id);
        if (item) {
            if (n > 0 && item.qty >= p.stock) return alert('Stok terbatas!');
            item.qty += n;
            if (item.qty <= 0) cart = cart.filter(x => x.id !== id);
        } else if (n > 0) {
            cart.push({ ...p, qty: 1 });
        }
        renderGrid(getAktifCat());
        renderCart();
    }

    function renderCart() {
        const list = document.getElementById('cartList');
        list.innerHTML = cart.length ? cart.map(i => `
            <div class="cart-item">
                <div>
                    <div style="font-weight:600; font-size:13px;">${i.name}</div>
                    <div class="cart-ctrl">
                        <button class="btn-circle" onclick="updateQty(${i.id}, -1)">-</button>
                        <span style="font-size:13px; font-weight:bold;">${i.qty}</span>
                        <button class="btn-circle" onclick="updateQty(${i.id}, 1)">+</button>
                    </div>
                </div>
                <div style="font-weight:700;">Rp ${(i.qty * i.price).toLocaleString('id-ID')}</div>
            </div>`) : '<p style="text-align:center; padding:30px; color:#ccc;">Belum ada item</p>';
        
        const total = cart.reduce((a, b) => a + (b.qty * b.price), 0);
        document.getElementById('totalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function setMethod(el) {
        document.querySelectorAll('.m-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
    }

    async function prosesBayar() {
        const name = document.getElementById('custName').value;
        const method = document.querySelector('.m-pill.active').innerText;
        
        if (!cart.length) return alert('Pilih produk dulu!');
        if (!name) return alert('Isi nama pelanggan!');

        try {
            const res = await fetch('controller/transaksi/transaksi_controller.php?action=save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nama_customer: name, metode_pembayaran: method, items: cart })
            });
            const data = await res.json();
            if (data.success) {
                currentInvoice = data.invoice;
                showStruk(name, method);
            } else alert(data.message);
        } catch (e) { alert('Terjadi kesalahan sistem'); }
    }

    function showStruk(cust, method) {
        const total = cart.reduce((a, b) => a + (b.qty * b.price), 0);
        const now = new Date();
        const date = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        
        let itemsHtml = cart.map(i => `<div class="item-row"><span>${i.name} (x${i.qty})</span><span>${(i.qty * i.price).toLocaleString('id-ID')}</span></div>`).join('');

        document.getElementById('strukArea').innerHTML = `
            <div class="struk-card">
                <div class="struk-header"><h2>U-MANAGE POS</h2></div>
                <div class="struk-divider"></div>
                <div style="font-size:11px; margin-bottom:10px;">
                    <div>No: ${currentInvoice}</div>
                    <div>Tgl: ${date} ${time}</div>
                    <div>Pelanggan: ${cust}</div>
                    <div style="font-weight:bold;">Metode: ${method}</div>
                </div>
                <div class="struk-divider"></div>
                ${itemsHtml}
                <div class="struk-divider"></div>
                <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:15px; margin-top:10px;">
                    <span>TOTAL</span>
                    <span>Rp ${total.toLocaleString('id-ID')}</span>
                </div>
                <p style="text-align:center; font-size:10px; margin-top:20px;">Terima Kasih</p>
            </div>`;
        document.getElementById('modalStruk').style.display = 'flex';
    }

    function downloadStruk() {
        const element = document.getElementById('strukArea');
        html2pdf().set({
            margin: 10,
            filename: 'Struk-' + currentInvoice + '.pdf',
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(element).save();
    }

    function filterCat(c, el) {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        renderGrid(c);
    }

    function getAktifCat() {
        const a = document.querySelector('.cat-btn.active');
        return a ? (a.innerText.toLowerCase() === 'semua' ? 'all' : a.innerText.toLowerCase()) : 'all';
    }

    renderGrid();
</script>
</body>
</html>