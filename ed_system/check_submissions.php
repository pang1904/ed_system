<?php
include 'config.php';
session_start();

// 1. ตรวจสอบสิทธิ์ (Security Check)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    die("หน้านี้สำหรับครูเท่านั้น <a href='admin_menu.php'>กลับหน้าหลัก</a>");
}

// 2. คำสั่ง SQL ที่สำคัญ (ต้อง JOIN 4 ตารางเพื่อให้ได้ข้อมูลครบ)
// - submissions (การส่งงาน)
// - students/users (ข้อมูลนักเรียน)
// - assignments (โจทย์ที่ครูตั้งไว้)
$sql = "SELECT 
            s.id AS submission_id,
            u.name AS student_name,
            a.title AS task_title,
            a.type AS task_type,
            a.attachment_link AS question_file, -- ไฟล์โจทย์ที่ครูลง
            s.file_link AS student_file,        -- ไฟล์ที่นักเรียนส่ง
            s.submitted_at
        FROM submissions s
        JOIN students st ON s.student_id = st.id
        JOIN users u ON st.user_id = u.id
        JOIN assignments a ON s.assignment_id = a.id
        ORDER BY s.submitted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ระบบตรวจงานและข้อสอบ</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    * { box-sizing: border-box; font-family: 'Sarabun', sans-serif; }
    body {
        margin: 0;
        padding: 30px;
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        min-height: 100vh;
    }

    .container {
        max-width: 1100px;
        margin: auto;
        background: #ffffff;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
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

    p {
        font-size: 14px;
        margin-bottom: 20px;
    }

    a.back-link {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
        margin-left: 10px;
    }

    a.back-link:hover { text-decoration: underline; }

    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 12px;
        overflow: hidden;
    }

    thead th {
        background: #4f46e5;
        color: white;
        padding: 14px;
        font-size: 14px;
        text-align: left;
    }

    tbody td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }

    tbody tr:nth-child(even) td { background: #f9fafb; }
    tbody tr:hover td { background: #eef2ff; }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
    }

    .bg-exam { background: #dc3545; }       /* สีแดงสำหรับข้อสอบ */
    .bg-homework { background: #22c55e; }  /* สีเขียวสำหรับการบ้าน */

    .btn {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.25s;
    }

    .btn-view { background: #6366f1; color: white; }
    .btn-view:hover { opacity: 0.9; transform: translateY(-1px); }

    .btn-question { background: #6c757d; color: white; margin-right: 5px; }
    .btn-question:hover { opacity: 0.9; transform: translateY(-1px); }
</style>
</head>

<body>

<div class="container">
    <h2>✅ ตรวจรายการส่งงานและข้อสอบ</h2>
    <p>ครูผู้ตรวจ: <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
       <a href="admin_menu.php" class="back-link">⬅ กลับหน้าหลัก</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>วัน-เวลาที่ส่ง</th>
                <th>ชื่อนักเรียน</th>
                <th>หัวข้อ</th>
                <th>ประเภท</th>
                <th>ไฟล์โจทย์</th>
                <th>ไฟล์งานนักเรียน</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['submitted_at'])); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['task_title']); ?></td>
                        <td>
                            <span class="badge <?php echo ($row['task_type'] == 'exam') ? 'bg-exam' : 'bg-homework'; ?>">
                                <?php echo ($row['task_type'] == 'exam') ? 'ข้อสอบ' : 'การบ้าน'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['question_file']): ?>
                                <a href="<?php echo $row['question_file']; ?>" target="_blank" class="btn btn-question">📄 โจทย์</a>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo $row['student_file']; ?>" target="_blank" class="btn btn-view">🔍 เปิดตรวจงาน</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center; color:#6b7280;">ยังไม่มีข้อมูลการส่งงานในขณะนี้</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
