<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once __DIR__ . '/../API/connection/koneksi.php';
$userId = $_SESSION['user_id'];

try {
    $stmt = $database_connection->prepare("
        SELECT 
            ID_user as id,
            nama_UMKM,
            username,
            email,
            no_hp,
            alamat,
            foto
        FROM users
        WHERE ID_user = ?
        LIMIT 1
    ");
    
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Data user tidak ditemukan";
        exit;
    }
    
} catch (Exception $e) {
    error_log("Profile Error: " . $e->getMessage());
    echo "Terjadi kesalahan saat mengambil data user";
    exit;
}
?>

<style>
.profile-container {
    max-width: 1400px;
    margin: 0 auto;
}

.profile-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
    overflow: hidden;
    padding-top: 20px;
}

.profile-photo-section {
    padding: 0 40px;
}

.photo-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 5px;
}

.profile-photo-container {
    position: relative;
}

.profile-photo {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 4px solid #f0f0f0;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 50px;
    color: #880d0d;
    overflow: hidden;
}

.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 38px;
    height: 38px;
    background: #880d0d;
    border-radius: 50%;
    border: 3px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #fff;
    transition: all 0.3s;
}

.upload-btn:hover {
    background: #6d0a0a;
    transform: scale(1.1);
}

.upload-input {
    display: none;
}

.photo-info h2 {
    font-size: 40px;
    color: #333;
    margin-bottom: 5px;
}

.photo-info p {
    color: #999;
    font-size: 14px;
}

.profile-form {
    padding: 20px 40px 40px;
}

.section-title {
    font-size: 18px;
    font-weight: 500;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-icon {
    color: #880d0d;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: span 1;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: #666;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.label-icon {
    color: #880d0d;
}

.form-input {
    padding: 12px 16px;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    font-size: 14px;
    background: #fff;
    color: #333;
    transition: all 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: #880d0d;
    box-shadow: 0 0 0 3px rgba(136,13,13,.1);
}

.form-input:disabled,
.form-input[readonly] {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
}

.form-textarea {
    min-height: 25px;
    resize: vertical;
    font-family: inherit;
}

.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #999;
    font-size: 14px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    padding-top: 25px;
    border-top: 1.5px solid #f0f0f0;
}

.btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
}

.btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.btn-primary {
    background: #880d0d;
    color: #fff;
}

.btn-secondary {
    background: #f8f9fa;
    border: 1px solid #e5e5e5;
    color: #666;
}

.success-message {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 12px 20px;
    border-radius: 10px;
    margin: 0 40px 20px 40px;
    display: none;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.success-message.show {
    display: flex;
}

.error-message {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
    padding: 12px 20px;
    border-radius: 10px;
    margin: 0 40px 20px 40px;
    display: none;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.error-message.show {
    display: flex;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-group.full-width {
        grid-column: span 1;
    }
    
    .photo-wrapper {
        flex-direction: column;
        text-align: center;
    }

    .profile-photo-section,
    .profile-form {
        padding-left: 25px;
        padding-right: 25px;
    }

    .success-message,
    .error-message {
        margin: 0 25px 20px 25px;
    }
}
</style>

<div class="profile-container">
    <div class="profile-card">

        <div class="success-message" id="success-message">
            <i class="fas fa-check-circle"></i>
            <span>Profil berhasil diperbarui!</span>
        </div>

        <div class="error-message" id="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <span id="error-text">Terjadi kesalahan!</span>
        </div>

        <div class="profile-photo-section">
            <div class="photo-wrapper">

                <div class="profile-photo-container">
                    <div class="profile-photo" id="profile-photo">
                        <?php if (!empty($user['foto'])): ?>
                           <img src="uploads/profile/<?= htmlspecialchars($user['foto']) ?>" alt="Profile Photo" onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-circle\'></i>'">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>

                    <label for="photo-upload" class="upload-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="photo-upload" class="upload-input" accept="image/*">
                </div>

                <div class="photo-info">
                    <h2 id="display-name"><?= htmlspecialchars($user['nama_UMKM'] ?? 'Nama UMKM') ?></h2>
                </div>

            </div>
        </div>

        <form id="profile-form" method="POST" action="controller/profile/update_data.php">
            <div class="profile-form">

                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

                <div class="section-title">
                    <i class="fas fa-info-circle section-icon"></i>
                    <span>Informasi Dasar</span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-store label-icon"></i>
                            Nama UMKM
                        </label>
                        <input type="text" class="form-input" id="umkm-name" name="nama_UMKM" 
                               value="<?= htmlspecialchars($user['nama_UMKM'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-at label-icon"></i>
                            Username
                        </label>
                        <input type="text" class="form-input" id="username" name="username" 
                               value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope label-icon"></i>
                            Email
                        </label>
                        <input type="email" class="form-input" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone label-icon"></i>
                            No. Handphone
                        </label>
                        <input type="tel" class="form-input" id="phone" name="no_hp" 
                               value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" required>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt label-icon"></i>
                            Alamat Lengkap
                        </label>
                        <textarea class="form-input form-textarea" id="address" name="alamat" required><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-lock section-icon"></i>
                    <span>Keamanan Akun</span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-key label-icon"></i>
                            Password Baru
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="new-password" name="new_password" 
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            <span class="toggle-password" onclick="togglePassword('new-password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-check-double label-icon"></i>
                            Konfirmasi Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="confirm-password" name="confirm_password" 
                                   placeholder="Ulangi password baru">
                            <span class="toggle-password" onclick="togglePassword('confirm-password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">
                        <i class="fas fa-undo"></i>
                        <span>Reset</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('photo-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    console.log('File dipilih:', file.name, 'Size:', file.size, 'Type:', file.type);
    if (file.size > 2 * 1024 * 1024) {
        showError('Ukuran file terlalu besar! Maksimal 2MB.');
        this.value = '';
        return;
    }

    if (!file.type.startsWith('image/')) {
        showError('File harus berupa gambar!');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById('profile-photo').innerHTML = 
            `<img src="${event.target.result}" alt="Profile Photo">`;
    };
    reader.readAsDataURL(file);
    uploadPhoto(file);
});

function uploadPhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);

    console.log('Mengirim foto ke server...');

    fetch('controller/profile/upload_photo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text(); 
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            console.log('Response data:', data);
            
            if (data.success) {
                showSuccess('Foto berhasil diupload!');
            } else {
                showError('Gagal mengupload foto: ' + data.message);
            }
        } catch (e) {
            console.error('JSON Parse Error:', e);
            console.error('Response text:', text);
            showError('Terjadi kesalahan saat mengupload foto. Periksa console untuk detail.');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showError('Terjadi kesalahan jaringan: ' + error.message);
    });
}

function togglePassword(id, element) {
    const input = document.getElementById(id);
    const icon = element.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation
document.getElementById('profile-form').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    if (newPassword || confirmPassword) {
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            showError('Password baru dan konfirmasi password tidak cocok!');
            return false;
        }

        if (newPassword.length < 6) {
            e.preventDefault();
            showError('Password minimal 6 karakter!');
            return false;
        }
    }
});

function showSuccess(message) {
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = message || 'Profil berhasil diperbarui!';
    successMsg.classList.add('show');
    const umkmName = document.getElementById('umkm-name').value;
    document.getElementById('display-name').textContent = umkmName;
    window.scrollTo({ top: 0, behavior: 'smooth' });

    setTimeout(() => {
        successMsg.classList.remove('show');
    }, 3000);
}

function showError(message) {
    const errorMsg = document.getElementById('error-message');
    errorMsg.querySelector('#error-text').textContent = message;
    errorMsg.classList.add('show');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    setTimeout(() => {
        errorMsg.classList.remove('show');
    }, 5000);
}

window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('status') === 'success') {
        showSuccess('Profil berhasil diperbarui!');
        const newUrl = window.location.pathname + '?page=akun';
        window.history.replaceState({}, document.title, newUrl);
    } else if (urlParams.get('status') === 'error') {
        const errorMsg = '<?= $_SESSION["error_message"] ?? "Terjadi kesalahan saat menyimpan data!" ?>';
        showError(errorMsg);
        const newUrl = window.location.pathname + '?page=akun';
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>