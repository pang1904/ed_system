<?php
include 'config.php';
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

// เพิ่มรายวิชา (เฉพาะครู)
if ($role == 'teacher' && isset($_POST['add_subject'])) {
    $name = trim($_POST['subject_name']);
    if ($name != "") {
        mysqli_query($conn, "INSERT INTO subjects (name) VALUES ('$name')");
        $success = "สร้างหลักสูตรสำเร็จ";
    }
}

// ดึงข้อมูลวิชา
$result = mysqli_query($conn, "SELECT * FROM subjects");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายวิชา / หลักสูตร</title>
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
        padding: 30px;
    }

    .container {
        max-width: 800px;
        margin: auto;
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        margin-top: 0;
        color: #1f2937;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    li {
        background: #f9fafb;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }

    .teacher-box {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px dashed #e5e7eb;
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
        margin-bottom: 15px;
        font-size: 15px;
    }

    input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }

    button {
        padding: 10px 22px;
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        border: none;
        border-radius: 10px;
        color: #ffffff;
        font-weight: 600;
        cursor: pointer;
        transition: 0.25s;
    }

    button:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .success {
        background: #dcfce7;
        color: #166534;
        padding: 10px 14px;
        border-radius: 10px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .back {
        margin-top: 25px;
        text-align: right;
    }

    .back a {
        text-decoration: none;
        font-weight: 600;
        color: #4f46e5;
    }
</style>
</head>

<body>

<div class="container">

    <h2>📘 รายวิชา / หลักสูตร</h2>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <ul>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <li><?php echo htmlspecialchars($row['name']); ?></li>
        <?php endwhile; ?>
    </ul>

    <?php if ($role == 'teacher'): ?>
        <div class="teacher-box">
            <h3>➕ สร้างหลักสูตรใหม่ (เฉพาะครู)</h3>
            <form method="post">
                <label>ชื่อวิชา</label>
                <input type="text" name="subject_name" placeholder="เช่น คอมพิวเตอร์เบื้องต้น" required>
                <button type="submit" name="add_subject">บันทึก</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="back">
        <a href="admin_menu.php">⬅ กลับหน้าเมนูหลัก</a>
    </div>

</div>

</body>
</html>
