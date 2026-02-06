<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // ในงานจริงควรใช้ password_hash
    $role = $_POST['role'];

    // 1. ตรวจสอบ Email ซ้ำ
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email นี้มีในระบบแล้ว');</script>";
    } else {
        // 2. บันทึกลงตาราง users
        $sql_user = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $sql_user)) {
            $user_id = mysqli_insert_id($conn); // ดึง ID ล่าสุดที่เพิ่งสร้าง
            
            // 3. บันทึกลงตารางแยกตาม Role ตาม Schema
            if ($role == 'student') {
                mysqli_query($conn, "INSERT INTO students (user_id, class_level) VALUES ('$user_id', 'Unassigned')");
            } else if ($role == 'teacher') {
                mysqli_query($conn, "INSERT INTO teachers (user_id, department) VALUES ('$user_id', 'General')");
            }
            
            echo "<script>alert('ลงทะเบียนสำเร็จ'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สมัครสมาชิก - Education Platform</title>
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

    .register-card {
        background: #ffffff;
        width: 100%;
        max-width: 420px;
        padding: 35px 30px;
        border-radius: 18px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .register-card h2 {
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

    input, select {
        width: 100%;
        padding: 11px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        margin-bottom: 18px;
        font-size: 15px;
        transition: 0.2s;
    }

    input:focus, select:focus {
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

    .login-link {
        text-align: center;
        margin-top: 18px;
        font-size: 14px;
    }

    .login-link a {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
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

<div class="register-card">
    <div class="badge">Education Platform</div>
    <h2>ลงทะเบียนนักเรียน / ครู</h2>

    <form method="post">
        <label>ชื่อ-นามสกุล</label>
        <input type="text" name="name" placeholder="กรอกชื่อ-นามสกุล" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="example@email.com" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="อย่างน้อย 6 ตัวอักษร" required>

        <label>บทบาทผู้ใช้งาน</label>
        <select name="role">
            <option value="student">นักเรียน</option>
            <option value="teacher">ครู</option>
            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
        </select>

        <button type="submit" name="register">สมัครสมาชิก</button>
    </form>

    <div class="login-link">
        มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
    </div>
</div>

</body>
</html>
