<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../API/connection/koneksi.php';

$userId = $_SESSION['user_id'] ?? null;
$nama_display = 'User';
$email_display = 'user@mail.com';
$foto_user = '';

if ($userId) {
    try {
        $stmt = $database_connection->prepare("SELECT nama_UMKM, email, foto FROM users WHERE ID_user = ? LIMIT 1");
        $stmt->execute([$userId]);
        $userHeader = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userHeader) {
            $nama_display = $userHeader['nama_UMKM'];
            $email_display = $userHeader['email'];
            $foto_user = $userHeader['foto'];
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

$inisial = strtoupper(substr($nama_display, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif; 
        }
        
        .top-header {
            position: fixed; 
            top: 20px; 
            left: 330px; 
            right: 30px;
            background: #ffffff; 
            padding: 12px 25px; 
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            z-index: 999;
        }

        .welcome-section h1 { 
            font-size: 22px; 
            color: #1a1a1a; 
            font-weight: 700; 
            letter-spacing: -0.5px; 
        }

        .header-actions { 
            display: flex; 
            align-items: center; 
            gap: 25px; 
        }

        .search-box {
            display: flex; 
            align-items: center; 
            background: #f1f1f4;
            padding: 10px 18px; 
            border-radius: 12px; 
            gap: 12px; 
            min-width: 320px;
            border: 1px solid transparent; 
            transition: all 0.2s;
        }

        .search-box:focus-within {
            background: #fff; 
            border-color: #880d0d; 
            box-shadow: 0 0 0 3px rgba(136, 13, 13, 0.1);
        }

        .search-box input { 
            border: none; 
            background: none; 
            outline: none; 
            width: 100%; 
            font-size: 14px; 
            color: #1c1c1e; 
        }

        .profile-container { 
            position: relative; 
        }

        .profile-section {
            display: flex; 
            align-items: center; 
            gap: 12px; 
            cursor: pointer;
            padding: 4px 8px; 
            border-radius: 50px; 
            transition: 0.2s;
        }

        .profile-section:hover { 
            background: #f8f8f8; 
        }

        .profile-text .name { 
            font-size: 28px; 
            font-weight: 600; 
            color: #1a1a1a; 
        }

        .profile-avatar {
            width: 50px; 
            height: 50px; 
            border-radius: 60%;
            background: linear-gradient(135deg, #880d0d 0%, #b31212 100%);
            display: flex; 
            align-items: center; 
            justify-content: center;
            color: #ffffff; 
            font-weight: 700; 
            font-size: 16px; 
            overflow: hidden;
        }

        .profile-avatar img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }

        .profile-dropdown {
            position: absolute; 
            top: calc(100% + 15px); 
            right: 0; 
            width: 240px;
            background: #ffffff; 
            border-radius: 14px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: none; 
            flex-direction: column; 
            overflow: hidden; 
            border: 1px solid rgba(0,0,0,0.05);
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown { 
            from { opacity: 0; transform: translateY(-10px); } 
            to { opacity: 1; transform: translateY(0); } 
        }

        .profile-dropdown.active { 
            display: flex; 
        }

        .dropdown-header { 
            padding: 18px; 
            background: #f8f9fa; 
            border-bottom: 1px solid #eee; 
        }

        .dropdown-header .user-name { 
            font-size: 14px; 
            font-weight: 700; 
            color: #1a1a1a; 
            display: block; 
        }

        .dropdown-header .user-email { 
            font-size: 12px; 
            color: #8e8e93; 
        }

        .dropdown-menu { 
            padding: 8px; 
        }

        .dropdown-item {
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 10px 12px;
            color: #48484a; 
            text-decoration: none; 
            font-size: 14px; 
            border-radius: 8px;
            transition: 0.2s;
        }

        .dropdown-item:hover { 
            background: #f1f1f4; 
            color: #880d0d; 
        }

        .dropdown-item.logout { 
            color: #ff3b30; 
            border-top: 1px solid #f2f2f7; 
            border-radius: 0; 
            margin-top: 5px; 
        }

        #no-data-notif {
            display: none;
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #880d0d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            z-index: 1000;
        }
    </style>
</head>
<body>

<header class="top-header">
    <div class="welcome-section">
        <h1><?php echo $currentTitle ?? 'Dashboard'; ?></h1>
    </div>

    <div class="header-actions">
        <div class="search-box">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="globalSearch" placeholder="Cari data..." onkeyup="universalSearch()">
        </div>

        <div class="profile-container">
            <div class="profile-section" id="profileBtn">
                <div class="profile-text">
                    <span class="name"><?= htmlspecialchars($nama_display) ?></span>
                </div>
                <div class="profile-avatar">
                    <?php if (!empty($foto_user)): ?>
                        <img src="uploads/profile/<?= htmlspecialchars($foto_user) ?>" onerror="this.parentElement.innerHTML='<?= $inisial ?>'">
                    <?php else: ?>
                        <?= $inisial ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-dropdown" id="profileDropdown">
                <div class="dropdown-header">
                    <span class="user-name"><?= htmlspecialchars($nama_display) ?></span>
                    <span class="user-email"><?= htmlspecialchars($email_display) ?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="index.php?page=akun" class="dropdown-item">Profil Saya</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="no-data-notif">Data tidak ditemukan</div>

<script>
    const btn = document.getElementById('profileBtn');
    const menu = document.getElementById('profileDropdown');
    
    btn.addEventListener('click', (e) => { 
        e.stopPropagation(); 
        menu.classList.toggle('active'); 
    });

    document.addEventListener('click', () => {
        menu.classList.remove('active');
    });

    function universalSearch() {
        const input = document.getElementById("globalSearch");
        const filter = input.value.toLowerCase();
        let foundAny = false;
        const tableRows = document.querySelectorAll("table tr:not(:first-child)");
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = "";
                foundAny = true;
            } else {
                row.style.display = "none";
            }
        });

        const cards = document.querySelectorAll(".product-card, .card, [class*='card']");
        cards.forEach(card => {
            if (card.closest('.top-header') || card.closest('.sidebar')) return;

            const text = card.textContent.toLowerCase();
            if (text.includes(filter)) {
                card.style.display = "";
                foundAny = true;
            } else {
                card.style.display = "none";
            }
        });
        const notif = document.getElementById("no-data-notif");
        if (!foundAny && filter !== "") {
            notif.style.display = "block";
        } else {
            notif.style.display = "none";
        }
    }
</script>
</body>
</html>