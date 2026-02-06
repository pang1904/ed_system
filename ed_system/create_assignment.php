<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'teacher') { die("หน้านี้สำหรับครูเท่านั้น"); }

if (isset($_POST['create'])) {
    $subject_id = $_POST['subject_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type']; // รับค่าประเภท
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = $_POST['due_date'];
    
    $attachment = "";
    // ส่วนของการอัปโหลดไฟล์โจทย์จากครู
    if (!empty($_FILES["attachment"]["name"])) {
        $target_dir = "uploads/questions/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $attachment = $target_dir . time() . "_" . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment);
    }

    $sql = "INSERT INTO assignments (subject_id, title, type, description, attachment_link, due_date) 
            VALUES ('$subject_id', '$title', '$type', '$description', '$attachment', '$due_date')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('ประกาศสำเร็จ!'); window.location='admin_menu.php';</script>";
    }
}
$subjects = mysqli_query($conn, "SELECT * FROM subjects");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สร้างโจทย์ข้อสอบ / การบ้าน</title>
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
        max-width: 700px;
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

    form {
        margin-top: 20px;
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

    input[type="text"],
    select,
    textarea,
    input[type="date"],
    input[type="file"] {
        width: 100%;
        padding: 11px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 15px;
    }

    textarea {
        resize: vertical;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }

    .radio-group {
        display: flex;
        gap: 20px;
        margin-top: 6px;
    }

    .radio-group input {
        width: auto;
    }

    button {
        background: linear-gradient(135deg, #4f46e5, #22c55e);
        color: white;
        border: none;
        padding: 12px 28px;
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

    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>

<div class="container">
    <h2>📝 สร้างข้อสอบ / การบ้าน</h2>

    <form method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label>วิชา</label>
            <select name="subject_id" required>
                <?php while($s = mysqli_fetch_assoc($subjects)): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>หัวข้อ</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>ประเภท</label>
            <div class="radio-group">
                <label><input type="radio" name="type" value="homework" checked> การบ้าน</label>
                <label><input type="radio" name="type" value="exam"> ข้อสอบ</label>
            </div>
        </div>

        <div class="form-group">
            <label>รายละเอียด</label>
            <textarea name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label>ไฟล์โจทย์ (ถ้ามี)</label>
            <input type="file" name="attachment">
        </div>

        <div class="form-group">
            <label>กำหนดส่ง</label>
            <input type="date" name="due_date" required>
        </div>

        <button type="submit" name="create">ตกลง</button>
        <a href="admin_menu.php" class="back-link">⬅ กลับหน้าหลัก</a>
    </form>
</div>

</body>
</html>
