<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
         body {
            background-color: #f4f4f4;
            color: var(--text);
            height: 100vh;
            overflow: auto;
            padding: 5px;
        }

        .hpp-main-wrapper { 
            width: 100%; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif; 
            background: #f1f5f9; 
            padding: 0px; 
        }
        .hpp-container { 
            max-width: 95%; 
            margin: 0 auto; 
            background: #fff; 
            border-radius: 20px; 
            box-shadow: 0 4px 25px rgba(0,0,0,0.08); 
        }
        .form-content { 
            padding: 30px; 
        }
        .grid-top { 
            display: grid; 
            grid-template-columns: 4fr 1fr; 
            gap: 25px; 
            margin-bottom: 30px; 
        }
        .hpp-form-group { 
            display: flex; 
            flex-direction: column; 
            gap: 10px; 
        }
        .hpp-form-group label { 
            font-size: 11px; 
            font-weight: 800; 
            color: #475569; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .hpp-main-wrapper input, .hpp-main-wrapper select { 
            width: 100%; 
            padding: 14px; 
            border: 1.5px solid #e2e8f0; 
            border-radius: 12px; 
            outline: none; 
            transition: 0.2s; 
        }
        .hpp-main-wrapper input:focus { 
            border-color: #880d0d; 
            box-shadow: 0 0 0 3px rgba(136, 13, 13, 0.1); 
        }
        .section-card { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 18px; 
            padding: 25px; 
            margin-bottom: 25px; 
        }
        .section-title { 
            font-weight: 800; 
            font-size: 15px; 
            margin-bottom: 20px; 
            display: flex; 
            justify-content: space-between; 
            border-bottom: 2px solid #f1f5f9; 
            padding-bottom: 12px; 
            color: #1e293b; 
        }
        .item-row { 
            display: grid; 
            grid-template-columns: 2fr 1fr 1fr 45px; 
            gap: 15px; 
            background: #f8fafc; 
            padding: 15px; 
            border-radius: 12px; 
            margin-bottom: 12px; 
            align-items: center; 
            border: 1px solid #f1f5f9; 
        }
        .radio-wrapper { 
            display: flex; 
            gap: 8px; 
            margin-bottom: 5px; 
        }
        .radio-option { 
            flex: 1; 
            position: relative; 
        }
        .radio-option input { 
            position: absolute; 
            opacity: 0; 
            cursor: pointer; 
            height: 0; 
            width: 0; 
        }
        .radio-label { 
            display: block; 
            padding: 8px; 
            text-align: center; 
            background: #f1f5f9; 
            border-radius: 8px; 
            font-size: 11px; 
            font-weight: 700; 
            color: #64748b; 
            cursor: pointer; 
            transition: 0.2s; 
            border: 1px solid transparent; 
        }
        .radio-option input:checked ~ .radio-label { 
            background: #880d0d; 
            color: white; 
            border-color: #880d0d; 
        }
        .btn-add { 
            width: 100%; 
            padding: 14px; 
            border: 2px dashed #cbd5e1; 
            background: #fff; 
            color: #64748b; 
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 700; 
            transition: 0.3s; 
        }
        .btn-add:hover { 
            background: #fdf2f2; 
            border-color: #880d0d; 
            color: #880d0d; 
        }
        .calc-trigger { 
            width: 100%; 
            padding: 22px; 
            background: #880d0d; 
            color: white; 
            border: none; 
            border-radius: 15px; 
            font-weight: 800; 
            cursor: pointer; 
            font-size: 17px; 
            box-shadow: 0 4px 15px rgba(136, 13, 13, 0.2); 
        }
        .btn-del { 
            color: #ef4444; 
            cursor: pointer; 
            text-align: center; 
            font-size: 20px; 
            font-weight: bold; 
        }
        .results-wrapper { 
            display: none; 
            margin-top: 35px; 
            padding: 25px; 
            background: #fff; 
            border-radius: 20px; 
            border: 2px solid #f1f5f9; 
        }
        .results-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
        }
        .result-card { 
            padding: 20px; 
            border-radius: 15px; 
            background: #f8fafc; 
            border-left: 6px solid #64748b; 
        }
        .res-label { 
            font-size: 11px; 
            color: #64748b; 
            font-weight: 800; 
            display: block; 
            margin-bottom: 8px; 
            text-transform: uppercase; 
        }
        .res-value { 
            font-size: 18px; 
            font-weight: 800; 
            color: #1e293b; 
        }
        .footer-btns { 
            display: flex; 
            justify-content: flex-end; 
            padding: 25px 30px; 
            background: #f8fafc; 
            gap: 15px; 
            border-radius: 0 0 20px 20px; 
        }
    </style>
</head>
<body>

<div class="hpp-main-wrapper">
    <div class="hpp-container">
        <div class="form-content">
            <div class="grid-top">
                <div class="hpp-form-group">
                    <label>Nama Produk / Jasa</label>
                    <input type="text" id="namaProd" placeholder="Masukkan nama produk...">
                </div>
                <div class="hpp-form-group">
                    <label>Jumlah Produksi (Qty)</label>
                    <input type="number" id="qtyProd" value="1" min="1">
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">1. Biaya Bahan Baku <span id="sumBahan" style="color:#880d0d">Rp 0</span></div>
                <div id="boxBahan"></div>
                <button class="btn-add" onclick="addNewRow('boxBahan', false)">+ Tambah Item Bahan Baku</button>
            </div>

            <div class="section-card">
                <div class="section-title">2. Biaya Tenaga Kerja <span id="sumKerja" style="color:#880d0d">Rp 0</span></div>
                <div id="boxKerja"></div>
                <button class="btn-add" onclick="addNewRow('boxKerja', true)">+ Tambah Tenaga Kerja</button>
            </div>

            <div class="section-card">
                <div class="section-title">3. Biaya Overhead <span id="sumOverhead" style="color:#880d0d">Rp 0</span></div>
                <div id="boxOverhead"></div>
                <button class="btn-add" onclick="addNewRow('boxOverhead', true)">+ Tambah Biaya Overhead</button>
            </div>

            <div class="section-card">
                <div class="section-title">4. Penyesuaian & Profit</div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div class="hpp-form-group">
                        <label>Biaya Tak Terduga</label>
                        <div class="radio-wrapper">
                            <label class="radio-option">
                                <input type="radio" name="modeTakTerduga" value="percent" checked>
                                <span class="radio-label">% (Persen)</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="modeTakTerduga" value="rp">
                                <span class="radio-label">RP (Nominal)</span>
                            </label>
                        </div>
                        <input type="number" id="valTakTerduga" value="0">
                    </div>

                    <div class="hpp-form-group">
                        <label>Margin Keuntungan</label>
                        <div class="radio-wrapper">
                            <label class="radio-option">
                                <input type="radio" name="modeMargin" value="percent" checked>
                                <span class="radio-label">% (Persen)</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="modeMargin" value="rp">
                                <span class="radio-label">RP (Nominal)</span>
                            </label>
                        </div>
                        <input type="number" id="valMargin" value="0">
                    </div>

                    <div class="hpp-form-group">
                        <label>Pajak</label>
                        <div class="radio-wrapper">
                            <label class="radio-option">
                                <input type="radio" name="modePajak" value="percent" checked>
                                <span class="radio-label">% (Pajak)</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="modePajak" value="rp">
                                <span class="radio-label">RP (Fixed)</span>
                            </label>
                        </div>
                        <input type="number" id="valPajak" value="0">
                    </div>
                </div>
            </div>

            <button class="calc-trigger" onclick="hitungHPP()">HITUNG HASIL AKHIR</button>

            <div class="results-wrapper" id="displayHasil">
                <div class="results-grid">
                    <div class="result-card" style="border-left-color: #880d0d;">
                        <span class="res-label">Total HPP Produksi</span>
                        <span class="res-value" id="outTotal">Rp 0</span>
                    </div>
                    <div class="result-card" style="border-left-color: #475569;">
                        <span class="res-label">HPP per Unit</span>
                        <span class="res-value" id="outUnit">Rp 0</span>
                    </div>
                    <div class="result-card" style="border-left-color: #f59e0b;">
                        <span class="res-label">Margin Satuan</span>
                        <span class="res-value" id="outMarginVal">Rp 0</span>
                    </div>
                    <div class="result-card" style="border-left-color: #3b82f6;">
                        <span class="res-label">Harga Jual per Unit</span>
                        <span class="res-value" id="outHargaUnit">Rp 0</span>
                    </div>
                    <div class="result-card" style="border-left-color: #16a34a; background: #f0fdf4;">
                        <span class="res-label">Harga Jual + Pajak</span>
                        <span class="res-value" id="outHargaFinal" style="color: #166534;">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-btns">
            <button style="background:#e2e8f0; padding: 12px 30px; border-radius: 10px; cursor: pointer; border: none; font-weight:700;" onclick="window.location.reload()">RESET</button>
            <button style="background:#880d0d; color: white; padding: 12px 30px; border-radius: 10px; cursor: pointer; border: none; font-weight:700;" onclick="simpanData()">SIMPAN DATA</button>
        </div>
    </div>
</div>

<script>
function addNewRow(boxId, hasSatuan) {
    const box = document.getElementById(boxId);
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `
        <input type="text" placeholder="Nama..." class="in-nama">
        <input type="number" placeholder="Harga" class="in-harga" oninput="liveUpdate()">
        ${hasSatuan ? `
            <select class="in-satuan" onchange="liveUpdate()">
                <option value="Per Hari">Per Hari</option>
                <option value="Per Bulan">Per Bulan</option>
                <option value="Per Jam">Per Jam</option>
            </select>
        ` : '<div></div>'}
        <div class="btn-del" onclick="this.parentElement.remove(); liveUpdate()">×</div>
    `;
    box.appendChild(div);
}

function fmt(n) { 
    return "Rp " + new Intl.NumberFormat('id-ID').format(Math.round(n)); 
}

function getSectionTotal(boxId, isSpecial) {
    let total = 0;
    const rows = document.querySelectorAll(`#${boxId} .item-row`);
    rows.forEach(row => {
        let harga = parseFloat(row.querySelector('.in-harga').value) || 0;
        if (isSpecial) {
            const satuan = row.querySelector('.in-satuan').value;
            if (satuan === 'Per Bulan') harga = harga / 30;
            else if (satuan === 'Per Jam') harga = harga * 8;
        }
        total += harga;
    });
    return total;
}

function liveUpdate() {
    document.getElementById('sumBahan').innerText = fmt(getSectionTotal('boxBahan', false));
    document.getElementById('sumKerja').innerText = fmt(getSectionTotal('boxKerja', true));
    document.getElementById('sumOverhead').innerText = fmt(getSectionTotal('boxOverhead', true));
}

function hitungHPP() {
    const qty = parseFloat(document.getElementById('qtyProd').value) || 1;
    const totalBahan = getSectionTotal('boxBahan', false);
    const totalKerja = getSectionTotal('boxKerja', true);
    const totalOverhead = getSectionTotal('boxOverhead', true);

    const modeTakTerduga = document.querySelector('input[name="modeTakTerduga"]:checked').value;
    const inputTakTerduga = parseFloat(document.getElementById('valTakTerduga').value) || 0;
    let biayaTakTerduga = (modeTakTerduga === 'percent') 
        ? (totalKerja + totalOverhead) * (inputTakTerduga / 100) 
        : inputTakTerduga;

    const totalHPP = totalBahan + totalKerja + totalOverhead + biayaTakTerduga;
    const hppPerUnit = totalHPP / qty;

    const modeMargin = document.querySelector('input[name="modeMargin"]:checked').value;
    const inputMargin = parseFloat(document.getElementById('valMargin').value) || 0;
    const marginValue = (modeMargin === 'percent') 
        ? hppPerUnit * (inputMargin / 100) 
        : inputMargin;

    const hargaJualPerUnit = hppPerUnit + marginValue;

    const modePajak = document.querySelector('input[name="modePajak"]:checked').value;
    const inputPajak = parseFloat(document.getElementById('valPajak').value) || 0;
    const pajakValue = (modePajak === 'percent') 
        ? hargaJualPerUnit * (inputPajak / 100) 
        : inputPajak;

    const hargaFinal = hargaJualPerUnit + pajakValue;

    document.getElementById('displayHasil').style.display = 'block';
    document.getElementById('outTotal').innerText = fmt(totalHPP);
    document.getElementById('outUnit').innerText = fmt(hppPerUnit);
    document.getElementById('outMarginVal').innerText = fmt(marginValue);
    document.getElementById('outHargaUnit').innerText = fmt(hargaJualPerUnit);
    document.getElementById('outHargaFinal').innerText = fmt(hargaFinal);

    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

function simpanData() {
    const namaProd = document.getElementById('namaProd').value;
    if (!namaProd) {
        alert("Nama produk tidak boleh kosong!");
        return;
    }

    const parseRP = (id) => {
        const el = document.getElementById(id);
        if (!el || el.innerText === 'Rp 0') return 0;
        return parseFloat(el.innerText.replace(/[^\d]/g, '')) || 0;
    };

    const totalBahan = getSectionTotal('boxBahan', false);
    const totalKerja = getSectionTotal('boxKerja', true);
    const totalOverhead = getSectionTotal('boxOverhead', true);
    const totalHPP = parseRP('outTotal');

    const payload = {
        nama_produk: namaProd,
        jumlah_produksi: parseFloat(document.getElementById('qtyProd').value) || 1,
        bahan_baku: totalBahan,
        tenaga_kerja: totalKerja,
        overhead: totalOverhead,
        biaya_tak_terduga: totalHPP - (totalBahan + totalKerja + totalOverhead),
        margin: parseRP('outMarginVal'),
        pajak: parseRP('outHargaFinal') - parseRP('outHargaUnit')
    };

    fetch('controller/hpp_controller.php?action=save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            alert("Berhasil: " + res.message);
        } else {
            alert("Gagal: " + res.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Kesalahan koneksi server.");
    });
}
</script>

</body>
</html>