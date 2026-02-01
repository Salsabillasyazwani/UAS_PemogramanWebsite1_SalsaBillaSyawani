<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/salsauas/API/connection/koneksi.php';

$user_id = $_SESSION['user_id'] ?? 0;

try {
    $sql = "SELECT * FROM hpp WHERE ID_user = ? ORDER BY id DESC";
    $stmt = $database_connection->prepare($sql);
    $stmt->execute([$user_id]);
    $riwayat_hpp = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    $riwayat_hpp = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat HPP - U-Manage</title>
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

        .report-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            max-width: 1400px;
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
            transition: 0.3s;
        }

        .btn-qf.active {
            background: var(--maroon);
            color: white;
            border-color: var(--maroon);
        }

        input[type="date"] {
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            font-family: inherit;
        }

        .btn-apply {
            background: var(--maroon);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
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

        .table-wrapper { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            min-width: 1200px;
        }

        thead th {
            background: var(--maroon);
            color: white;
            text-align: left;
            padding: 15px;
            font-size: 11px;
            text-transform: uppercase;
        }

        tbody td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        .badge-hpp {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
        }

        .text-success { color: #16a34a; font-weight: bold; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 1100px;
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .modal-header { padding: 20px; color: white; font-weight: bold; }
        .modal-body { padding: 30px; }
        .modal-footer { padding: 15px; background: #f8fafc; display: flex; justify-content: center; gap: 10px; }
        .btn-dl { padding: 12px 25px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; color: white; width: 100%; }

        #previewContainer {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-top: 15px;
        }

        #previewTable { width: 100%; font-size: 10px; border-collapse: collapse; }
        #previewTable th { background: #f1f5f9; color: #475569; padding: 10px; position: sticky; top: 0; }
        #previewTable td { padding: 10px; border-bottom: 1px solid #f1f5f9; text-align: left; }
    </style>
</head>
<body>

<div class="report-card">
    <div class="top-toolbar">
        <button class="btn-export btn-pdf" onclick="openPop('PDF')"><i class="fas fa-file-pdf"></i> Export PDF</button>
        <button class="btn-export btn-excel" onclick="openPop('Excel')"><i class="fas fa-file-excel"></i> Export Excel</button>
    </div>

    <div class="filter-container">
        <div class="filter-group">
            <label>Quick Filter</label>
            <div class="quick-filter-btns">
                <button class="btn-qf active" onclick="setQuick('all', this)">Semua</button>
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
        <div class="filter-group">
            <button class="btn-apply" onclick="applyFilter()">
                <i class="fas fa-filter"></i> Terapkan Filter
            </button>
        </div>
    </div>

    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Bahan</th>
                    <th>Tenaga</th>
                    <th>Overhead</th>
                    <th>Darurat</th>
                    <th>Total HPP</th>
                    <th>HPP/Unit</th>
                    <th>Margin</th>
                    <th>Harga Jual</th>
                    <th>Pajak</th>
                    <th>Final</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php if (!empty($riwayat_hpp)): ?>
                    <?php foreach ($riwayat_hpp as $row): 
                        $row_date = date('Y-m-d', strtotime($row['created_at'] ?? date('Y-m-d')));
                    ?>
                        <tr data-date="<?= $row_date ?>">
                            <td style="color: var(--text-muted)">#H-<?= $row['id'] ?></td>
                            <td><b><?= htmlspecialchars($row['nama_produk']) ?></b></td>
                            <td><span class="badge-hpp"><?= number_format($row['jumlah_produksi']) ?></span></td>
                            <td><?= number_format($row['bahan_baku']) ?></td>
                            <td><?= number_format($row['tenaga_kerja']) ?></td>
                            <td><?= number_format($row['overhead']) ?></td>
                            <td><?= number_format($row['biaya_tak_terduga']) ?></td>
                            <td style="font-weight: bold;"><?= number_format($row['total_hpp']) ?></td>
                            <td><?= number_format($row['hpp_per_unit']) ?></td>
                            <td><?= number_format($row['margin']) ?></td>
                            <td><?= number_format($row['harga_jual']) ?></td>
                            <td><?= number_format($row['pajak']) ?></td>
                            <td class="text-success"><?= number_format($row['harga_jual_pajak']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="noDataRow">
                        <td colspan="13" style="text-align: center; padding: 30px; color: #94a3b8;">
                            <i class="fas fa-history" style="font-size: 24px;"></i><br>Belum ada riwayat perhitungan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalDl" class="modal">
    <div class="modal-content">
        <div id="modalHead" class="modal-header">Export Dokumen</div>
        <div class="modal-body">
            <i id="modalIcon" style="font-size: 40px; margin-bottom: 15px;"></i>
            <p id="modalMsg" style="font-size: 14px; color: #444; margin-bottom: 10px;"></p>
            <div id="previewContainer">
                <table id="previewTable"></table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-dl" id="btnDownload" onclick="processDownload()">Unduh Laporan Sekarang</button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modalDl');
    let currentMode = '';
    let filterMode = 'all';

    function setQuick(mode, el) {
        document.querySelectorAll('.btn-qf').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        filterMode = mode;
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        applyFilter(); 
    }

    function clearQuickFilter() {
        document.querySelectorAll('.btn-qf').forEach(b => b.classList.remove('active'));
        filterMode = 'custom';
    }

    function applyFilter() {
        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;
        const rows = document.querySelectorAll('#tableBody tr:not(#noDataRow)');
        const today = new Date();
        today.setHours(0,0,0,0);

        rows.forEach(row => {
            const rowDate = new Date(row.getAttribute('data-date'));
            rowDate.setHours(0,0,0,0);
            let show = false;

            if (from && to) {
                const dFrom = new Date(from); dFrom.setHours(0,0,0,0);
                const dTo = new Date(to); dTo.setHours(0,0,0,0);
                show = (rowDate >= dFrom && rowDate <= dTo);
            } else {
                if (filterMode === 'all') show = true;
                else if (filterMode === 'today') show = rowDate.getTime() === today.getTime();
                else if (filterMode === '7days') {
                    const diff = (today - rowDate) / (1000 * 60 * 60 * 24);
                    show = diff >= 0 && diff <= 7;
                } else if (filterMode === 'month') {
                    show = rowDate.getMonth() === today.getMonth() && rowDate.getFullYear() === today.getFullYear();
                }
            }
            row.style.display = show ? '' : 'none';
        });
    }

    function openPop(type) {
        currentMode = type;
        modal.style.display = 'flex';
        const head = document.getElementById('modalHead');
        const icon = document.getElementById('modalIcon');
        const msg = document.getElementById('modalMsg');
        const btn = document.getElementById('btnDownload');
        const previewTable = document.getElementById('previewTable');
        
        const visibleRows = Array.from(document.querySelectorAll('#tableBody tr:not(#noDataRow)'))
                                 .filter(tr => tr.style.display !== 'none')
                                 .map(tr => tr.outerHTML).join('');
        
        const tableHead = document.querySelector('#mainTable thead').innerHTML;
        previewTable.innerHTML = `<thead>${tableHead}</thead><tbody>${visibleRows || '<tr><td colspan="13" style="text-align:center">Tidak ada data ditemukan</td></tr>'}</tbody>`;

        if(type === 'PDF') {
            head.style.backgroundColor = '#e11d48'; btn.style.backgroundColor = '#e11d48';
            icon.className = 'fas fa-file-pdf'; icon.style.color = '#e11d48';
            msg.innerText = "Pratinjau laporan PDF:";
        } else {
            head.style.backgroundColor = '#16a34a'; btn.style.backgroundColor = '#16a34a';
            icon.className = 'fas fa-file-excel'; icon.style.color = '#16a34a';
            msg.innerText = "Pratinjau laporan Excel:";
        }
    }

    function processDownload() {
        const btn = document.getElementById('btnDownload');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.disabled = true;
        setTimeout(() => {
            if (currentMode === 'PDF') exportToPDF();
            else exportToExcel();
            btn.innerHTML = 'Unduh Laporan Sekarang';
            btn.disabled = false;
            modal.style.display = 'none';
        }, 800);
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        doc.text("Laporan Riwayat HPP - U-Manage", 14, 15);
        doc.autoTable({ 
            html: '#previewTable', 
            startY: 25, 
            theme: 'grid', 
            headStyles: { fillColor: [136, 13, 13] }, 
            styles: { fontSize: 7 } 
        });
        doc.save("Riwayat_HPP.pdf");
    }

    function exportToExcel() {
        const workbook = XLSX.utils.table_to_book(document.getElementById("previewTable"), { sheet: "Riwayat HPP" });
        XLSX.writeFile(workbook, "Riwayat_HPP.xlsx");
    }

    window.onclick = function(event) { if (event.target == modal) modal.style.display = 'none'; }
</script>
</body>
</html>