<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

:root {
    --primary-color: #880d0d;
    --primary-hover: #ff1313;
    --bg-light: #F5F3FF;
    --text-dark: #000000;
    --text-gray: #000000;
    --white: #FFFFFF;
    --border: #E5E7EB;
    --sidebar-width: 280px;
}

body {
    background-color: #f9fafb;
}

.mobile-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1100;
    background: var(--primary-color);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    align-items: center;
    justify-content: center;
}

.sidebar {
    position: fixed;
    left: 25px;
    top: 18px;
    bottom: 20px;
    width: var(--sidebar-width);
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    padding: 20px;
    transition: all 0.3s ease;
    z-index: 1000;
}

.sidebar-header {
    display: flex;
    align-items: center;
    padding: 8px 0; 
    height: 10;
    border-bottom: 1px solid transparent; 
    
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.logo img {
    width: 90px;
    height: 90px;
    object-fit: contain;
}

.logo-text {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-dark);
}

.nav-menu {
    flex: 1;
    overflow-y: auto;
    padding-right: 5px;
    margin-top: 10px;
    margin-bottom: 10px;
}

.nav-menu::-webkit-scrollbar { width: 4px; }
.nav-menu::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.nav-item { margin-bottom: 5px; }

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    color: var(--text-gray);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.nav-link:hover {
    background: var(--bg-light);
    color: var(--primary-color);
}

.nav-link.active {
    background: var(--primary-color) !important;
    color: var(--white) !important;
}

.nav-link i {
    font-size: 18px;
    width: 24px;
    text-align: center;
    color: inherit;
}

.nav-text {
    font-size: 14px;
    font-weight: 600;
}

.arrow {
    margin-left: auto;
    font-size: 11px;
    transition: transform 0.3s;
}

.nav-item.open .arrow { transform: rotate(90deg); }

.submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding-left: 15px;
}

.nav-item.open .submenu { max-height: 400px; }

.submenu .nav-link {
    padding: 8px 16px;
    font-size: 13px;
    margin-top: 2px;
}

.sidebar-footer {
    border-top: 1px solid var(--border);
    padding: 10px 0 0; 
    margin-top: auto;
}

.logout-btn {
    color: #000000 !important; 
}

.logout-btn:hover {
    background: #fff5f5 !important;
    color: var(--primary-color) !important;
}

.main-content {
    margin-left: calc(var(--sidebar-width) + 40px);
    padding: 30px;
    transition: all 0.3s ease;
}

@media (max-width: 1024px) {
    .main-content { margin-left: 0; padding-top: 80px; }
    .mobile-toggle { display: flex; }
    .sidebar {
        left: -320px;
        top: 0; bottom: 0;
        border-radius: 0;
        width: 280px;
    }
    .sidebar.mobile-active { left: 0; }
}
</style>

<button class="mobile-toggle" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileMenu()"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo">
                <img src="<?= BASE_URL ?>/assets/images/lm.png" alt="Logo">
            </div>
            <span class="logo-text">U-Manage</span>
        </div>
    </div>

    <nav class="nav-menu">
        <div class="nav-item">
            <a href="?page=dashboard" class="nav-link">
                <i class="fas fa-th-large"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </div>

        <div class="nav-item">
            <div class="nav-link" onclick="toggleSubmenu(this)">
                <i class="fas fa-box"></i>
                <span class="nav-text">Produk</span>
                <i class="fas fa-chevron-right arrow"></i>
            </div>
            <div class="submenu">
                <a href="?page=input-produk" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    <span class="nav-text">Input Produk</span>
                </a>
                <a href="?page=daftar-produk" class="nav-link">
                    <i class="fas fa-list"></i>
                    <span class="nav-text">Daftar Produk</span>
                </a>
            </div>
        </div>

        <div class="nav-item">
            <a href="?page=transaksi" class="nav-link">
                <i class="fas fa-exchange-alt"></i>
                <span class="nav-text">Transaksi</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="?page=hitunghpp" class="nav-link">
                <i class="fas fa-calculator"></i>
                <span class="nav-text">Hitung HPP</span>
            </a>
        </div>

        <div class="nav-item">
            <div class="nav-link" onclick="toggleSubmenu(this)">
                <i class="fas fa-file-alt"></i>
                <span class="nav-text">Laporan</span>
                <i class="fas fa-chevron-right arrow"></i>
            </div>
            <div class="submenu">
                <a href="?page=laporan-produk" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Daftar Produk</span>
                </a>
                <a href="?page=mutasi-produk" class="nav-link">
                    <i class="fas fa-sync"></i>
                    <span class="nav-text">Mutasi Produk</span>
                </a>
                <a href="?page=laporan-transaksi" class="nav-link">
                    <i class="fas fa-receipt"></i>
                    <span class="nav-text">Laporan Transaksi</span>
                </a>
                <a href="?page=transaksi-detail" class="nav-link">
                    <i class="fas fa-info-circle"></i>
                    <span class="nav-text">Transaksi Detail</span>
                </a>
                <a href="?page=riwayat-hpp" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span class="nav-text">Riwayat HPP</span>
                </a>
            </div>
        </div>

        <div class="nav-item">
            <a href="?page=akun" class="nav-link">
                <i class="fas fa-user-circle"></i>
                <span class="nav-text">Akun</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="controller/auth/logout.php" class="nav-link logout-btn" onclick="konfirmasiLogout(event)">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<script>
function toggleSubmenu(element) {
    const parent = element.parentElement;
    parent.classList.toggle('open');
}

function toggleMobileMenu() {
    document.getElementById('sidebar').classList.toggle('mobile-active');
    document.getElementById('sidebarOverlay').classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 'dashboard'; 
    const links = document.querySelectorAll('.nav-link');
    
    links.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        
        if (href === '?page=' + currentPage) {
            link.classList.add('active');
            const submenu = link.closest('.submenu');
            if (submenu) {
                submenu.parentElement.classList.add('open');
            }
        }
    });
});

function konfirmasiLogout(event) {
    event.preventDefault(); 
    if (confirm("Apakah Anda yakin ingin keluar?")) {
        const logoutUrl = "controller/auth/logout.php"; 
        fetch(logoutUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "auth/loginregister.php";
                } else {
                    alert("Gagal logout: " + data.message);
                }
            })
            .catch(error => {
                window.location.href = "auth/loginregister.php";
            });
    }
}
</script>