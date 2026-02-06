<?php
include 'config.php';
session_start();

// ตรวจสอบ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// --- ส่วนของครู: จัดการการบันทึกคะแนน ---
if ($role == 'teacher') {
    if (isset($_POST['submit_grade'])) {
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $sub_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
        $score = mysqli_real_escape_string($conn, $_POST['score']);

        // ตรวจสอบว่าเคยกรอกเกรดวิชานี้ให้นักเรียนคนนี้ไปหรือยัง (Update หรือ Insert)
        $check_sql = "SELECT id FROM grades WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        $check_res = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_res) > 0) {
            $sql = "UPDATE grades SET score = '$score' WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        } else {
            $sql = "INSERT INTO grades (student_id, subject_id, score) VALUES ('$student_id', '$sub_id', '$score')";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('บันทึกคะแนนเรียบร้อยแล้ว');</script>";
        }
    }

    // ดึงรายชื่อนักเรียนและวิชามาใส่ใน Dropdown
    $students_list = mysqli_query($conn, "SELECT s.id, u.name FROM students s JOIN users u ON s.user_id = u.id");
    $subjects_list = mysqli_query($conn, "SELECT * FROM subjects");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ระบบจัดการเกรดและคะแนน</title>
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
        max-width: 900px;
        margin: auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 20px;
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

    .card {
        background: #f9fafb;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    select, input {
        width: 100%;
        padding: 11px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 15px;
    }

    select:focus, input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }

    .actions {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-top: 10px;
    }

    button {
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        color: white;
        border: none;
        padding: 11px 26px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.25s;
    }

    button:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    a.back {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        border-radius: 12px;
        overflow: hidden;
    }

    th {
        background: #4f46e5;
        color: white;
        padding: 14px;
        font-size: 14px;
    }

    td {
        padding: 14px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }

    tr:hover td {
        background: #f3f4f6;
    }

    .pass {
        color: #16a34a;
        font-weight: 700;
    }

    .fail {
        color: #dc2626;
        font-weight: 700;
    }

    .empty {
        text-align: center;
        color: #6b7280;
        padding: 20px;
    }

    .footer-link {
        margin-top: 25px;
        text-align: right;
    }
</style>
</head>

<body>

<div class="container">

<?php if ($role == 'teacher'): ?>

    <h2>👨‍🏫 สำหรับอาจารย์: กรอกคะแนนนักเรียน</h2>

    <div class="card">
        <form method="post">
            <div class="form-group">
                <label>เลือกนักเรียน</label>
                <select name="student_id" required>
                    <option value="">-- รายชื่อนักเรียน --</option>
                    <?php while($row = mysqli_fetch_assoc($students_list)): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>เลือกรายวิชา</label>
                <select name="subject_id" required>
                    <option value="">-- รายชื่อวิชา --</option>
                    <?php while($row = mysqli_fetch_assoc($subjects_list)): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>คะแนน (0–100)</label>
                <input type="number" name="score" min="0" max="100" step="0.01" required>
            </div>

            <div class="actions">
                <button type="submit" name="submit_grade">บันทึกคะแนน</button>
                <a href="admin_menu.php" class="back">⬅ กลับหน้าหลัก</a>
            </div>
        </form>
    </div>

<?php else: ?>

    <h2>🎓 ผลการเรียนของคุณ: <?php echo htmlspecialchars($_SESSION['name']); ?></h2>

    <table>
        <thead>
            <tr>
                <th>รายวิชา</th>
                <th>คะแนน</th>
                <th>การประเมิน</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT s.name as subject_name, g.score 
                FROM grades g 
                JOIN subjects s ON g.subject_id = s.id 
                WHERE g.student_id = (SELECT id FROM students WHERE user_id = '$user_id')";
        $res = mysqli_query($conn, $sql);

        if (mysqli_num_rows($res) > 0) {
            while($row = mysqli_fetch_assoc($res)) {
                $score = $row['score'];
                $grade = ($score >= 50)
                    ? "<span class='pass'>ผ่าน</span>"
                    : "<span class='fail'>ไม่ผ่าน</span>";

                echo "<tr>
                        <td>".htmlspecialchars($row['subject_name'])."</td>
                        <td>{$score}</td>
                        <td>{$grade}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3' class='empty'>ยังไม่มีข้อมูลคะแนน</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="footer-link">
        <a href="admin_menu.php" class="back">⬅ กลับหน้าหลัก</a>
    </div>

<?php endif; ?>

</div>

</body>
</html>
