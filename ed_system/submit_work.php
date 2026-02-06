<?php
include 'config.php';
session_start();
if ($_SESSION['role'] != 'student') { die("หน้านี้สำหรับนักเรียนเท่านั้น"); }

// (ส่วนประมวลผลการส่งงานคงเดิมจากที่คุณมี แต่เปลี่ยน $target_dir เป็น uploads/submissions/ เพื่อความระเบียบ)

$query = "SELECT a.*, s.name as subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id ORDER BY a.due_date ASC";
$assignments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>งานที่ได้รับมอบหมาย</title>
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
        max-width: 1000px;
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        border-radius: 14px;
        overflow: hidden;
    }

    thead th {
        background: #4f46e5;
        color: #ffffff;
        padding: 14px;
        font-size: 14px;
    }

    tbody td {
        padding: 14px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
        font-size: 14px;
    }

    tbody tr:hover td {
        background: #f3f4f6;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
    }

    .bg-exam {
        background: #ef4444;
    }

    .bg-work {
        background: #22c55e;
    }

    .subject {
        font-weight: 700;
        color: #1f2937;
    }

    .download a {
        text-decoration: none;
        font-weight: 600;
        color: #4f46e5;
    }

    .download a:hover {
        text-decoration: underline;
    }

    .select-btn {
        background: #6366f1;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.25s;
    }

    .select-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    hr {
        border: none;
        height: 1px;
        background: #e5e7eb;
        margin: 30px 0;
    }

    .upload-box {
        background: #f9fafb;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
    }

    .upload-box h3 {
        margin-top: 0;
        color: #1f2937;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    input[type="text"], input[type="file"] {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
    }

    button.upload-btn {
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        border: none;
        color: white;
        padding: 10px 24px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
    }

    button.upload-btn:hover {
        opacity: 0.9;
    }

    .note {
        font-size: 12px;
        color: #6b7280;
        margin-top: 8px;
    }
</style>
</head>

<body>

<div class="container">

    <h2>📚 รายการงานที่ได้รับมอบหมาย</h2>

    <table>
        <thead>
            <tr>
                <th>ประเภท</th>
                <th>วิชา / หัวข้อ</th>
                <th>ไฟล์โจทย์</th>
                <th>กำหนดส่ง</th>
                <th>เลือก</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($assignments)): ?>
            <tr>
                <td>
                    <span class="badge <?php echo ($row['type'] == 'exam') ? 'bg-exam' : 'bg-work'; ?>">
                        <?php echo ($row['type'] == 'exam') ? 'ข้อสอบ' : 'การบ้าน'; ?>
                    </span>
                </td>
                <td>
                    <div class="subject"><?php echo htmlspecialchars($row['subject_name']); ?></div>
                    <?php echo htmlspecialchars($row['title']); ?>
                </td>
                <td class="download">
                    <?php if($row['attachment_link']): ?>
                        <a href="<?php echo $row['attachment_link']; ?>" target="_blank">📄 ดาวน์โหลด</a>
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td><?php echo $row['due_date']; ?></td>
                <td>
                    <button class="select-btn"
                        onclick="document.getElementById('as_id').value='<?php echo $row['id']; ?>'">
                        เลือกงาน
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <div class="upload-box">
        <h3>📤 ฟอร์มส่งไฟล์งาน</h3>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>ID งานที่เลือก</label>
                <input type="text" id="as_id" name="assignment_id" readonly required>
            </div>

            <div class="form-group">
                <label>เลือกไฟล์คำตอบ</label>
                <input type="file" name="fileToUpload" required>
            </div>

            <button type="submit" name="upload" class="upload-btn">ส่งงาน</button>
            <div class="note">รองรับไฟล์ PDF, DOCX, ZIP</div>
        </form>
    </div>
    <div class="back">
        <a href="admin_menu.php">⬅ กลับหน้าเมนูหลัก</a>
    </div>
    <div class="alert-box">
        <h1>⚠️ สิทธิ์เข้าถึงถูกจำกัด</h1>
        <p>หน้านี้สามารถเข้าถึงได้เฉพาะนักเรียนเท่านั้น</p>
        <a href="admin_menu.php">กลับหน้าหลัก</a>
    </div>

</div>

</body>
</html>
