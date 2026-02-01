<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/salsauas/API/connection/koneksi.php';

$user_id = $_SESSION['user_id'] ?? 0;
try {
    $query_list = "SELECT * FROM transaksi WHERE ID_user = ? ORDER BY tgl_transaksi DESC";
    $stmt_list = $database_connection->prepare($query_list);
    $stmt_list->execute([$user_id]);
    $transaksi = $stmt_list->fetchAll(PDO::FETCH_ASSOC) ?: []; 

} catch (PDOException $e) {
    $transaksi = [];
}
$id_transaksi = $_GET['id'] ?? 0;
$details = [];

if ($id_transaksi > 0) {
    try {
        $query_detail = "SELECT dt.*, p.nama_produk, p.harga_satuan, t.no_invoice, t.tgl_transaksi 
                        FROM detail_transaksi dt
                        JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                        JOIN produk p ON dt.id_produk = p.id_produk
                        WHERE dt.id_transaksi = ? AND t.ID_user = ?"; 
                  
        $stmt_detail = $database_connection->prepare($query_detail);
        $stmt_detail->execute([$id_transaksi, $user_id]);
        $details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        $details = [];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Detail - U-Manage</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        :root {
            --maroon: #880d0d;
            --white: #ffffff;
            --bg-gray: #f4f7f6;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }

        body { 
            background-color: var(--bg-gray); 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            padding: 5px; 
        }

        .header-title { 
            font-size: 24px; 
            font-weight: 500; 
            margin-bottom: 20px; 
            color: #333; 
        }
        
        .report-card { 
            background: var(--white); 
            border-radius: 15px; 
            padding: 25px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            max-width: 1200px; 
            margin: auto; 
        }

        .top-toolbar { 
            display: flex; 
            justify-content: flex-end; 
            gap: 10px; 
            margin-bottom: 20px; 
        }

        .filter-container { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            background: #fff; 
            padding: 20px; 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            margin-bottom: 25px; 
            align-items: flex-end; 
        }

        .filter-group { 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
        }

        .filter-group label { 
            font-size: 11px; 
            font-weight: 800; 
            color: var(--text-muted); 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quick-filter-btns { 
            display: flex; 
            gap: 8px; 
        }

        .btn-qf { 
            padding: 10px 15px; 
            border: 1px solid var(--border); 
            background: white; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 13px; 
            min-height: 40px;
            transition: 0.3s; 
        }
        
        .btn-qf.active { 
            background: var(--maroon); 
            color: white; 
            border-color: var(--maroon); 
        }

        input[type="date"] { 
            padding: 8px 12px; 
            border: 1px solid var(--border); 
            border-radius: 8px; 
            outline: none; 
            font-family: inherit;
            min-height: 40px;
            box-sizing: border-box;
        }

        .btn-apply { 
            background: var(--maroon); 
            color: white; 
            border: none; 
            padding: 0 20px; 
            border-radius: 8px; 
            font-weight: 600; 
            cursor: pointer; 
            font-size: 13px; 
            height: 40px;
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }

        .btn-export { 
            padding: 10px 18px; 
            border-radius: 8px; 
            border: none; 
            font-weight: 600; 
            color: white; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            font-size: 13px; 
        }

        .btn-pdf { background: #e11d48; }
        .btn-excel { background: #16a34a; }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }

        thead th { 
            background: var(--maroon); 
            color: white; 
            text-align: left; 
            padding: 15px; 
            font-size: 13px; 
            text-transform: uppercase; 
        }

        tbody td { 
            padding: 15px; 
            border-bottom: 1px solid var(--border); 
            font-size: 14px; 
            color: #333; 
        }
        
        .badge-method { 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: 800; 
            text-transform: uppercase; 
        }

        .bg-cash { 
            background: #eff6ff; 
            color: #1e40af; 
            border: 1px solid #dbeafe; 
        }

        .bg-qris { 
            background: #fffbeb; 
            color: #92400e; 
            border: 1px solid #fef3c7; 
        }

        #modalDl { 
            display: none; 
            position: fixed; 
            z-index: 9999; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
            backdrop-filter: blur(4px); 
            justify-content: center; 
            align-items: center; 
        }

        .modal-content { 
            background: white; 
            width: 90%; 
            max-width: 850px; 
            border-radius: 15px; 
            overflow: hidden; 
        }

        .modal-header { 
            padding: 15px 25px; 
            color: white; 
            font-weight: bold; 
            display: flex; 
            justify-content: space-between; 
        }

        .modal-body { 
            padding: 25px; 
            max-height: 70vh; 
            overflow-y: auto; 
        }

        .modal-footer { 
            padding: 15px; 
            background: #f8fafc; 
            display: flex; 
            justify-content: flex-end; 
        }
    </style>
</head>
<body>

<div class="report-card">
    <div class="top-toolbar">
        <button class="btn-export btn-pdf" onclick="openPop('PDF')">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button class="btn-export btn-excel" onclick="openPop('Excel')">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>

    <div class="filter-container">
        <div class="filter-group">
            <label>Quick Filter</label>
            <div class="quick-filter-btns">
                <button class="btn-qf active" id="btn-all" onclick="setQuick('all', this)">Semua</button>
                <button class="btn-qf" onclick="setQuick('today', this)">Hari Ini</button>
                <button class="btn-qf" onclick="setQuick('7days', this)">7 Hari</button>
                <button class="btn-qf" onclick="setQuick('month', this)">Bulan Ini</button>
            </div>
        </div>

        <div class="filter-group">
            <label>Dari Tanggal</label>
            <input type="date" id="dateFrom" oninput="clearQuickFilter()">
        </div>

        <div class="filter-group">
            <label>Sampai Tanggal</label>
            <input type="date" id="dateTo" oninput="clearQuickFilter()">
        </div>

        <button class="btn-apply" onclick="applyFilter()">
            <i class="fas fa-filter"></i> Terapkan Filter
        </button>
    </div>

    <table id="mainTable">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Nama Pelanggan</th>
                <th>Total Bayar</th>
                <th>Metode</th>
                <th>Tanggal Transaksi</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php foreach ($transaksi as $row): ?>
            <tr data-date="<?= date('Y-m-d', strtotime($row['tgl_transaksi'])) ?>">
                <td style="color: var(--maroon); font-weight: bold;">
                    <?= $row['no_invoice'] ?>
                </td>
                <td>
                    <?= htmlspecialchars($row['nama_customer']) ?>
                </td>
                <td style="font-weight: bold; color: var(--maroon);">
                    Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?>
                </td>
                <td>
                    <?php $m = strtoupper($row['metode_pembayaran']); ?>
                    <span class="badge-method <?= ($m == 'CASH') ? 'bg-cash' : 'bg-qris' ?>">
                        <?= $m ?>
                    </span>
                </td>
                <td>
                    <?= date('d/m/Y H:i', strtotime($row['tgl_transaksi'])) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalDl">
    <div class="modal-content">
        <div id="modalHead" class="modal-header">Preview Export</div>
        <div class="modal-body">
            <table id="previewTable"></table>
        </div>
        <div class="modal-footer">
            <button class="btn-apply" onclick="processDownload()" style="width: 100%; justify-content: center;">
                Unduh Laporan Sekarang
            </button>
        </div>
    </div>
</div>

<script>
    let currentMode = '';
    let filterMode = 'all';

    function clearQuickFilter() {
        document.querySelectorAll('.btn-qf').forEach(b => b.classList.remove('active'));
        filterMode = 'custom'; 
    }

    function setQuick(mode, el) {
        document.querySelectorAll('.btn-qf').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        filterMode = mode;
        // Kosongkan input tanggal jika memilih quick filter
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
    }

    function applyFilter() {
        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;
        const rows = document.querySelectorAll('#tableBody tr');
        const today = new Date();
        today.setHours(0,0,0,0);

        rows.forEach(row => {
            const rowDate = new Date(row.getAttribute('data-date'));
            rowDate.setHours(0,0,0,0);
            let show = false;

            if (from && to) {
                const dFrom = new Date(from);
                const dTo = new Date(to);
                show = (rowDate >= dFrom && rowDate <= dTo);
            } 
            else {
                if (filterMode === 'all') {
                    show = true;
                } else if (filterMode === 'today') {
                    show = rowDate.getTime() === today.getTime();
                } else if (filterMode === '7days') {
                    const diff = (today - rowDate) / (1000 * 60 * 60 * 24);
                    show = diff >= 0 && diff <= 7;
                } else if (filterMode === 'month') {
                    show = (rowDate.getMonth() === today.getMonth() && rowDate.getFullYear() === today.getFullYear());
                }
            }
            row.style.display = show ? '' : 'none';
        });
    }

    function openPop(type) {
        currentMode = type;
        document.getElementById('modalDl').style.display = 'flex';
        const visibleRows = Array.from(document.querySelectorAll('#tableBody tr'))
            .filter(tr => tr.style.display !== 'none')
            .map(tr => tr.outerHTML).join('');
        const head = document.querySelector('#mainTable thead').innerHTML;
        document.getElementById('previewTable').innerHTML = `<thead>${head}</thead><tbody>${visibleRows}</tbody>`;
        const mHead = document.getElementById('modalHead');
        mHead.style.backgroundColor = (type === 'PDF') ? '#e11d48' : '#16a34a';
    }

    function processDownload() {
        if (currentMode === 'PDF') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            doc.text("Laporan Transaksi", 14, 15);
            doc.autoTable({ 
                html: '#previewTable', 
                startY: 25, 
                theme: 'grid', 
                headStyles: {fillColor: [136, 13, 13]} 
            });
            doc.save("Laporan.pdf");
        } else {
            const wb = XLSX.utils.table_to_book(document.getElementById("previewTable"));
            XLSX.writeFile(wb, "Laporan.xlsx");
        }
        document.getElementById('modalDl').style.display = 'none';
    }

    window.onclick = (e) => { 
        if(e.target == document.getElementById('modalDl')) {
            document.getElementById('modalDl').style.display = 'none'; 
        }
    }
</script>
</body>
</html>