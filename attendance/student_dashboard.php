<?php
session_start();
include('db.php');

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$stmt = $conn->prepare("SELECT first_name, last_name, roll_no, faculty, course, username FROM students WHERE student_id=?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch attendance summary
$total = $conn->prepare("SELECT COUNT(*) AS total FROM attendance WHERE student_id=?");
$total->bind_param("i", $student_id);
$total->execute();
$total_classes = $total->get_result()->fetch_assoc()['total'];

$present = $conn->prepare("SELECT COUNT(*) AS present FROM attendance WHERE student_id=? AND status='Present'");
$present->bind_param("i", $student_id);
$present->execute();
$present_classes = $present->get_result()->fetch_assoc()['present'];

$percentage = $total_classes > 0 ? round(($present_classes / $total_classes) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard - QR Attendance System</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f2f5;
        margin: 0;
        padding: 0;
    }

    header {
        background: linear-gradient(90deg, #4CAF50, #007bff);
        color: white;
        padding: 40px 20px;
        text-align: center;
    }

    header h1 {
        margin: 0;
        font-size: 40px;
    }

    header p {
        font-size: 18px;
        margin-top: 10px;
    }

    .container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 0 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 25px 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        width: 300px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h2 {
        color: #4CAF50;
        margin-bottom: 15px;
    }

    .stat {
        font-size: 18px;
        margin: 10px 0;
    }

    .stat strong {
        font-size: 20px;
    }

    .low {
        color: red;
        font-weight: bold;
    }

    button {
        margin: 10px 5px 0 5px;
        padding: 12px 25px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #0056b3;
    }

    footer {
        text-align: center;
        padding: 20px;
        color: #555;
        font-size: 14px;
        margin-top: 40px;
    }

    @media(max-width: 650px) {
        .container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
</head>
<body>

<header>
    <h1>Welcome to QR Attendance System</h1>
    <p>Hi <?php echo htmlspecialchars($student['first_name'].' '.$student['last_name']); ?>! Here's your dashboard</p>
</header>

<div class="container">

    <!-- Profile Card -->
    <div class="card">
        <h2>Student Profile</h2>
        <p class="stat">Username: <strong><?php echo htmlspecialchars($student['username']); ?></strong></p>
        <p class="stat">Roll No: <strong><?php echo htmlspecialchars($student['roll_no']); ?></strong></p>
        <p class="stat">Faculty: <strong><?php echo htmlspecialchars($student['faculty']); ?></strong></p>
        <p class="stat">Course: <strong><?php echo htmlspecialchars($student['course']); ?></strong></p>
        <button onclick="window.location.href='logout.php'">Logout</button>
        <button onclick="window.location.href='scan_qr.php'">📷 Scan QR Code</button>
    </div>

    <!-- Attendance Card -->
    <div class="card">
        <h2>Attendance Summary</h2>
        <p class="stat">Total Classes: <strong><?php echo $total_classes; ?></strong></p>
        <p class="stat">Classes Attended: <strong><?php echo $present_classes; ?></strong></p>
        <p class="stat">
            Attendance Percentage:
            <strong class="<?php echo $percentage < 75 ? 'low' : ''; ?>">
                <?php echo $percentage; ?>%
            </strong>
        </p>
        <?php if ($percentage < 75): ?>
            <p class="low">⚠ Attendance below required minimum</p>
        <?php endif; ?>
        <button onclick="window.location.href='attendance.php'">View Attendance</button>
        
    </div>

</div>

<footer>
    &copy; <?php echo date("Y"); ?> QR Attendance System. All rights reserved.
</footer>

</body>
</html>
