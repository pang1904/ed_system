<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // เลียนแบบ Pseudo Code: user = DATABASE.find_user(username)
    $sql = "SELECT id, name, role, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // เลียนแบบ Pseudo Code: IF user EXISTS AND verify_password
    if ($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name']; // เพิ่มบรรทัดนี้
        header("Location:admin_menu.php");
    } else {
        echo "<script>alert('Invalid credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Login - Education Platform</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    * {
        box-sizing: border-box;
        font-family: "Segoe UI", Tahoma, sans-serif;
    }

    body {
        margin: 0;
        min-height: 100vh;
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background: #ffffff;
        width: 100%;
        max-width: 380px;
        padding: 35px 30px;
        border-radius: 18px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .login-card h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #1f2937;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        color: #374151;
    }

    input {
        width: 100%;
        padding: 11px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        margin-bottom: 18px;
        font-size: 15px;
        transition: 0.2s;
    }

    input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }

    button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        border: none;
        border-radius: 12px;
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .register-link {
        text-align: center;
        margin-top: 18px;
        font-size: 14px;
        color: #374151;
    }

    .register-link a {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .badge {
        display: inline-block;
        background: #eef2ff;
        color: #4f46e5;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        margin-bottom: 12px;
    }
</style>
</head>

<body>

<div class="login-card">
    <div class="badge">Education Platform</div>
    <h2>เข้าสู่ระบบ</h2>

    <form method="post">
        <label>Email</label>
        <input type="email" name="email" placeholder="example@email.com" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="รหัสผ่านของคุณ" required>

        <button type="submit">เข้าสู่ระบบ</button>

        <div class="register-link">
            ยังไม่มีบัญชีใช่ไหม? <a href="register.php">สมัครสมาชิก</a>
        </div>
    </form>
</div>

</body>
</html>
