<?php include $_SERVER['DOCUMENT_ROOT'] . '/salsauas/controller/dashboard_controller.php'; ?>

<style>
     body {
            background-color: #f4f4f4;
            color: var(--text);
            height: 50vh;
            overflow: auto;
            padding: 10px;
        }
    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 25px;
        padding: 20px;
        background-color: #f8fafc;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        position: relative;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .stat-icon-box {
        position: absolute;
        top: 25px;
        right: 20px;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 25px;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .card-title h2 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
    }

    .product-img {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        object-fit: cover;
    }

    /* Chart Styles */
    .chart-container {
        position: relative;
        height: 280px;
        margin-top: 20px;
    }

    .chart-legend {
        display: flex;
        gap: 20px;
        font-size: 12px;
        align-items: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .chart-grid-line {
        stroke: #f1f5f9;
        stroke-width: 1;
        stroke-dasharray: 4;
    }

    .chart-point {
        cursor: pointer;
        transition: all 0.2s;
    }

    .chart-point:hover {
        r: 6;
    }

    .tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 11px;
        pointer-events: none;
        display: none;
        z-index: 1000;
    }
</style>

<div class="dashboard-wrapper">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Produk Terjual Hari Ini</span>
            <p class="stat-value"><?= number_format($queryStats['terjual_hari'] ?? 0) ?></p>
            <div class="stat-icon-box" style="background:#eff6ff; color:#3b82f6;">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Produk Terjual Bulan Ini</span>
            <p class="stat-value"><?= number_format($queryStats['terjual_bulan'] ?? 0) ?> </p>
            <div class="stat-icon-box" style="background:#fff7ed; color:#f97316;">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Omset Hari Ini</span>
            <p class="stat-value">Rp <?= number_format($queryStats['omzet_hari'] ?? 0, 0, ',', '.') ?></p>
            <div class="stat-icon-box" style="background:#f0fdf4; color:#22c55e;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Omset Bulan Ini</span>
            <p class="stat-value">Rp <?= number_format($queryStats['omzet_bulan'] ?? 0, 0, ',', '.') ?></p>
            <div class="stat-icon-box" style="background:#faf5ff; color:#a855f7;">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <div class="bottom-grid">
        <!-- Grafik Penjualan -->
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2>Grafik Penjualan (7 Hari)</h2>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#ef4444"></span>
                        <span>Omset</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#3b82f6"></span>
                        <span>Produk</span>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <svg viewBox="0 0 320 180" width="100%" height="100%" style="overflow: visible;">
                    <line x1="20" y1="20" x2="20" y2="140" class="chart-grid-line"/>
                    <line x1="20" y1="140" x2="300" y2="140" class="chart-grid-line"/>
                    <line x1="20" y1="80" x2="300" y2="80" class="chart-grid-line"/>
                    <line x1="20" y1="20" x2="300" y2="20" class="chart-grid-line"/>
                    <polygon fill="rgba(239, 68, 68, 0.1)" points="<?= $points_omzet ?>300,140 20,140" />
                    <polygon fill="rgba(59, 130, 246, 0.1)" points="<?= $points_qty ?>300,140 20,140" />
                    <polyline fill="none" stroke="#ef4444" stroke-width="3" 
                              stroke-linecap="round" stroke-linejoin="round" 
                              points="<?= $points_omzet ?>" />
                    <polyline fill="none" stroke="#3b82f6" stroke-width="3" 
                              stroke-linecap="round" stroke-linejoin="round" 
                              points="<?= $points_qty ?>" />
                    <?php 
                    $omzet_points = explode(' ', trim($points_omzet));
                    foreach($omzet_points as $point): 
                        if(empty($point)) continue;
                        list($x, $y) = explode(',', $point);
                    ?>
                        <circle cx="<?= $x ?>" cy="<?= $y ?>" r="4" fill="#ef4444" 
                                stroke="white" stroke-width="2" class="chart-point"/>
                    <?php endforeach; ?>
                    <?php 
                    $qty_points = explode(' ', trim($points_qty));
                    foreach($qty_points as $point): 
                        if(empty($point)) continue;
                        list($x, $y) = explode(',', $point);
                    ?>
                        <circle cx="<?= $x ?>" cy="<?= $y ?>" r="4" fill="#3b82f6" 
                                stroke="white" stroke-width="2" class="chart-point"/>
                    <?php endforeach; ?>
                    <?php foreach($labels_grafik as $l): ?>
                        <text x="<?= $l['x'] ?>" y="160" font-size="10" fill="#64748b" 
                              text-anchor="middle"><?= $l['txt'] ?></text>
                    <?php endforeach; ?>
                    <?php if(isset($yaxis_labels)): ?>
                        <?php foreach($yaxis_labels['qty'] as $label): ?>
                            <text x="5" y="<?= $label['y'] ?>" font-size="9" fill="#3b82f6" 
                                  text-anchor="start"><?= $label['val'] ?></text>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </svg>
                
                <div class="tooltip" id="chartTooltip"></div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="card">
            <h2>10 Produk Terlaris</h2>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; color:#94a3b8; font-size:12px; border-bottom:2px solid #f1f5f9;">
                        <th style="padding:10px 0;">PRODUK</th>
                        <th>HARGA</th>
                        <th>STOK</th>
                        <th>TERJUAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($produk_terlaris)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">
                                Belum ada data produk terlaris
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produk_terlaris as $p): ?>
                        <tr style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:12px 0; display:flex; align-items:center; gap:12px;">
                                <img src="assets/images/produk/<?= $p['gambar'] ?>" 
                                     class="product-img" 
                                     onerror="this.src='assets/images/no-image.png'">
                                <span style="font-weight:600; font-size:14px; color:#1e293b;">
                                    <?= htmlspecialchars($p['nama_produk']) ?>
                                </span>
                            </td>
                            <td style="font-size:13px; color:#64748b;">
                                Rp <?= number_format($p['harga_satuan'], 0, ',', '.') ?>
                            </td>
                            <td style="font-size:13px; color:#64748b;">
                                <span style="padding:4px 8px; background:#f1f5f9; border-radius:6px;">
                                    <?= $p['stok'] ?>
                                </span>
                            </td>
                            <td style="font-weight:700; color:#22c55e; font-size:14px;">
                                <?= $p['terjual'] ?> unit
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartPoints = document.querySelectorAll('.chart-point');
    const tooltip = document.getElementById('chartTooltip');
    
    chartPoints.forEach((point, index) => {
        point.addEventListener('mouseenter', function(e) {
            const labels = <?= json_encode($labels_grafik ?? []) ?>;
            const pointIndex = Math.floor(index / 2); 
            const isOmset = index % 2 === 0;
            
            if (labels[pointIndex]) {
                tooltip.innerHTML = `
                    <strong>${labels[pointIndex].txt}</strong><br>
                    ${isOmset ? 'Omset: Rp ' + labels[pointIndex].omzet_val : 'Terjual: ' + labels[pointIndex].qty_val + ' unit'}
                `;
                tooltip.style.display = 'block';
                tooltip.style.left = (e.pageX + 10) + 'px';
                tooltip.style.top = (e.pageY - 30) + 'px';
            }
        });
        
        point.addEventListener('mouseleave', function() {
            tooltip.style.display = 'none';
        });
    });
});
</script>