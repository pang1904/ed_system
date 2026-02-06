<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// --- ส่วนของการ Export เป็นไฟล์ CSV ---
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=report_grades.csv');
    $output = fopen('php://output', 'w');
    // --- บรรทัดที่ต้องเพิ่มเพื่อแก้ภาษาต่างดาวใน Excel ---
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); 
    // ---------------------------------------------
    
    fputcsv($output, array('Student Name', 'Subject', 'Score')); // หัวตารางใน Excel

    $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN subjects sub ON g.subject_id = sub.id";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// --- ส่วนการแสดงผลหน้าเว็บ ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายงานสรุปผลการเรียน</title>
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

    .actions {
        margin-bottom: 20px;
    }

    .btn {
        display: inline-block;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: 0.25s;
        font-size: 14px;
    }

    .btn-export {
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        color: white;
    }

    .btn-export:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-back {
        background: #f3f4f6;
        color: #4f46e5;
        margin-left: 10px;
    }

    .btn-back:hover {
        background: #e0e7ff;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 12px;
        overflow: hidden;
    }

    th {
        background: #4f46e5;
        color: white;
        padding: 14px;
        font-size: 14px;
        text-align: left;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }

    tr:nth-child(even) td {
        background: #f9fafb;
    }

    tr:hover td {
        background: #eef2ff;
    }
</style>
</head>

<body>

<div class="container">

    <h2>📊 รายงานสรุปผลการเรียนทั้งหมด</h2>

    <div class="actions">
        <a href="?export=csv" class="btn btn-export">📥 ดาวน์โหลดเป็น Excel (CSV)</a>
        <a href="admin_menu.php" class="btn btn-back">⬅ กลับหน้าหลัก</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ชื่อนักเรียน</th>
                <th>รายวิชา</th>
                <th>คะแนน</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
                    FROM grades g
                    JOIN students s ON g.student_id = s.id
                    JOIN users u ON s.user_id = u.id
                    JOIN subjects sub ON g.subject_id = sub.id";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0){
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['s_name'])."</td>
                            <td>".htmlspecialchars($row['sub_name'])."</td>
                            <td>".$row['score']."</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center; color:#6b7280;'>ยังไม่มีข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>

</body>
</html>
