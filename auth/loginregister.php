<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-manage</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .main-footer {
            margin-left: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            border-top: none !important;
            width: 100%;
        }
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
            list-style: none;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(90deg, #ffffff, #fffcfc);
        }

        .container { 
            position: relative;
            width: 850px;
            height: 550px;
            background: #fff;
            margin: 20px;
            border-radius: 30px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .container h1 {
            font-size: 36px;
            margin: -10px 0;
        }

        .container p {
            font-size: 14.5px;
            margin: 15px 0;
        }

        form {   
            width: 100%;
        }

        .form-box {
            position: absolute;
            right: 0;
            width: 50%;
            height: 100%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            text-align: center;
            padding: 40px;
            z-index: 1;
            transition: 0.6s ease-in-out 0.6s, visibility 0s 1s;
        }

        .container.active .form-box {
            right: 50%;
        }

        .form-box.register {
            visibility: hidden;
        }

        .container.active .form-box.register {
            visibility: visible;
        }

        .input-box {
            position: relative;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .input-box input::placeholder {
            color: #888;
            font-weight: 400;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .toggle-panel .logo {
            position: relative;
            margin-bottom: 15px;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .toggle-panel .logo img {
            width: 220px;
            height: auto;
        }

        .forget-link {
            margin: -15px 0 15px;
        }

        .btn {
            width: 100%;
            height: 48px;
            background: #800C0C;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: none;
            outline: none;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
        }

        .toggle-box {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .toggle-box::before {
            content: '';
            position: absolute;
            left: -250%;
            width: 300%;
            height: 100%;
            background: #800C0C;
            border-radius: 150px;
            z-index: 2;
            transition: 1.2s ease-in-out;
        }

        .container.active .toggle-box::before {
            left: 50%;
        }

        .toggle-panel {
            position: absolute;
            width: 50%;
            height: 100%;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 3;
            padding: 40px;
            text-align: center;
            transition: 0.6s ease-in-out;
        }

        .toggle-panel.toggle-left {
            left: 0;
            transition-delay: 0.6s ;
        }

        .container.active .toggle-panel.toggle-left {
            left: -50%;
            transition-delay: 0.6s;
        }

        .toggle-panel.toggle-right {
            right: -50%;
            transition-delay: 0.6s;
        }

        .container.active .toggle-panel.toggle-right {
            right: 0;
            transition-delay: 0.6s;
        }

        .toggle-panel p {
            margin-bottom: 20px;
        }

        .toggle-panel .btn {
            width: 160px;
            height: 46px;
            background: transparent;
            border: 2px solid #fff;
            box-shadow: none;
        }

        .mobile-switch {
            display: none;
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }

        .mobile-switch a {
            color: #800C0C;
            font-weight: 600;
        }

        .form-box.login {
            z-index: 2;
        }

        .form-box.register {
            z-index: 1;
        }

        .container.active .form-box.login {
            z-index: 1;
        }

        .container.active .form-box.register {
            z-index: 2;
        }

        @media (max-width: 768px) {
            body {
                align-items: flex-start;
                padding: 0;
                margin: 0;
            }
            
            .container {
                width: 100%;
                min-height: 100vh;
                height: auto;
                border-radius: 0;
                margin: 0;
                overflow-y: auto;
            }
            
            .toggle-box {
                display: none ;
            }

            .form-box {
                position: relative;
                width: 100%;
                height: auto;
                min-height: 100vh;
                padding: 20px;
                display: none;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                right: 0 !important; 
            }

            .form-box.login {
                display: flex;
            }

            .container.active .form-box.login {
                display: none;
            }
            
            .container.active .form-box.register {
                display: flex;
                visibility: visible;
            }
            
            .form-box form {
                width: 100%;
                max-width: 400px;
            }
            
            .container h1 {
                font-size: 24px;
                margin-bottom: 15px;
            }
            
            .input-box {
                margin: 12px 0;
            }
            
            .input-box input {
                padding: 10px 45px 10px 15px;
                font-size: 14px;
            }
            
            .btn {
                height: 42px;
                font-size: 14px;
            }
            
            .mobile-switch {
                display: block ;
                margin-top: 12px;
                font-size: 13px;
                color: #333;
            }
            
            .mobile-switch a {
                color: #800C0C;
                font-weight: 600;
                text-decoration: underline;
            }
        }
    </style>
</head>
<body>
   <div class="container">
        <div class="form-box login">
            <form action="#">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" autocomplete="off" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="button" class="btn" id="loginBtn">Login</button>
                <p class="mobile-switch">
                    Don't have an Account?
                    <a href="#" id="toRegister">Register</a>
                </p>
            </form>
        </div>

        <div class="form-box register">
            <form action="#">
                <h1>Registrasi</h1>
                <div class="input-box">
                    <input type="text" name="name" placeholder="Name UMKM" >
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" placeholder="Email">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password" >
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Confirm Password" >
                    <i class="fa-solid fa-lock"></i>
                </div>
               <button type="submit" class="btn">Register</button>
                <p class="mobile-switch">
                    Already have an Account?
                    <a href="#" id="toLogin">Login</a>
                </p>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <div class="logo">
                    <img src="<?= BASE_URL ?>/assets/images/logo.png">
                </div>
                <h1>Hello, Welcome!</h1>
                <p>Don't have an Account?</p>
                <button type="button" class="btn register-btn">Register</button>
            </div>
            
            <div class="toggle-panel toggle-right">
                <div class="logo">
                    <img src="<?= BASE_URL ?>/assets/images/logo.png">
                </div>
                <h1>Welcome Back!</h1>
                <p>Already have an Account?</p>
                <button type="button" class="btn login-btn">Login</button> 
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/../layout/footer.php'; ?>
</body>
<script> 
document.addEventListener("DOMContentLoaded", () => {
   const API_URL = "http://aspel.cyou/salsauas/";
    const container = document.querySelector(".container");
    const registerBtn = document.querySelector(".register-btn");
    const loginBtn = document.querySelector(".login-btn");
    const toRegister = document.getElementById("toRegister");
    const toLogin = document.getElementById("toLogin");

    registerBtn?.addEventListener("click", () => {
        container.classList.add("active");
    });

    loginBtn?.addEventListener("click", () => {
        container.classList.remove("active");
    });

    toRegister?.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.add("active");
    });

    toLogin?.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.remove("active");
    });

    const loginButton = document.getElementById("loginBtn");
    
    loginButton?.addEventListener("click", async () => {  
        const usernameInput = document.querySelector(".login input[name='username']");
        const passwordInput = document.querySelector(".login input[type='password']");

        const username = usernameInput?.value.trim();
        const password = passwordInput?.value.trim();
        if (!username || !password) {
            alert("Username dan password wajib diisi!");
            return;
        }
        loginButton.disabled = true;
        loginButton.textContent = "Sedang login...";

        try {
            const res = await fetch(API_URL + "controller/auth/login.php", {
                method: "POST",
                credentials: "include",  
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ username, password })
            });

            const data = await res.json();
            
            if (data.success) {
                alert("Login berhasil!");
                window.location.href = API_URL + "index.php?page=dashboard";
            } else {
                alert(data.message || "Login gagal, periksa username/password.");
            }
        } catch (error) {
            console.error("Login Error:", error);
            alert("Terjadi kesalahan server. Coba lagi nanti.");
        } finally {
            loginButton.disabled = false;
            loginButton.textContent = "Login";
        }
    });

    const registerForm = document.querySelector(".register form");

    registerForm?.addEventListener("submit", async (e) => {
        e.preventDefault();

        const inputs = registerForm.querySelectorAll("input");
        const body = {
            nama_UMKM: inputs[0]?.value.trim(),
            username: inputs[1]?.value.trim(),
            email: inputs[2]?.value.trim(),
            password: inputs[3]?.value.trim(),
            confirm: inputs[4]?.value.trim()
        };

        if (Object.values(body).some(v => !v)) {
            alert("Semua field wajib diisi!");
            return;
        }

        if (body.password !== body.confirm) {
            alert("Konfirmasi password tidak cocok!");
            return;
        }

        if (body.password.length < 6) {
            alert("Password minimal 6 karakter!");
            return;
        }
        const submitBtn = registerForm.querySelector("button[type='submit']");
        submitBtn.disabled = true;
        submitBtn.textContent = "Sedang daftar...";

        try {
            const res = await fetch(API_URL + "controller/auth/register.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(body)
            });

            const data = await res.json();
            alert(data.message);

            if (data.success) {
                registerForm.reset();
                container.classList.remove("active");
            }
        } catch (error) {
            console.error("Register Error:", error);
            alert("Gagal melakukan registrasi. Coba lagi.");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "Daftar";
        }
    });
});
</script>
</html>