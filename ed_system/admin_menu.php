<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); }
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Main Menu - Education Platform</title>
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

    .dashboard {
        max-width: 900px;
        margin: auto;
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .header h1 {
        font-size: 22px;
        color: #1f2937;
        margin: 0;
    }

    .role-badge {
        background: #eef2ff;
        color: #4f46e5;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    hr {
        border: none;
        height: 1px;
        background: #e5e7eb;
        margin: 20px 0;
    }

    .menu {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .menu a {
        display: block;
        padding: 20px;
        background: #f9fafb;
        border-radius: 16px;
        text-decoration: none;
        color: #1f2937;
        font-weight: 600;
        transition: 0.25s;
        border: 1px solid #e5e7eb;
    }

    .menu a:hover {
        background: #eef2ff;
        border-color: #6366f1;
        transform: translateY(-3px);
    }

    .logout {
        margin-top: 30px;
        text-align: right;
    }

    .logout a {
        color: #ef4444;
        font-weight: 700;
        text-decoration: none;
        font-size: 14px;
    }

    .logout a:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>

<div class="dashboard">

    <div class="header">
        <h1>ยินดีต้อนรับ, <?php echo $_SESSION['name']; ?></h1>
        <div class="role-badge"><?php echo strtoupper($role); ?></div>
    </div>

    <hr>

    <div class="menu">
        <a href="admin_subjects.php">📘 รายวิชา / หลักสูตร</a>

        <?php if ($role == 'teacher'): ?>
            <a href="enter_grade.php">📝 บันทึกเกรดและประเมินผล</a>
            <a href="create_assignment.php">📚 ออกข้อสอบ / สั่งการบ้าน</a>
            <a href="check_submissions.php">✅ ตรวจงานนักเรียน</a>
        <?php else: ?>
            <a href="enter_grade.php">📊 ดูผลการเรียน</a>
            <a href="submit_work.php">📤 ดูข้อสอบและส่งการบ้าน</a>
        <?php endif; ?>

        <a href="export_report.php">📄 Report / Export ข้อมูล</a>
    </div>

    <div class="logout">
        <a href="logout.php">🚪 ออกจากระบบ</a>
    </div>

</div>

</body>
</html>