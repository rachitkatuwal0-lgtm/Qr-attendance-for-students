<?php
session_start();
include('db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

if (isset($_POST['qr_data'])) {
    // 1. Decode the JSON from QR
    $qr_json = json_decode($_POST['qr_data'], true);

    if (!$qr_json) {
        echo json_encode(["status" => "error", "message" => "Invalid QR Code format"]);
        exit();
    }

    $course = $qr_json['course'];
    $qr_date = $qr_json['date'];
    $secret = $qr_json['secret'];

    // 2. Validate Security Token
    if ($secret !== "my_secure_college_token") {
        echo json_encode(["status" => "error", "message" => "Fake QR Code detected!"]);
        exit();
    }

    // 3. Validate Date (Prevent scanning yesterday's code)
    $today = date("Y-m-d");
    if ($qr_date !== $today) {
        echo json_encode(["status" => "error", "message" => "This QR Code has expired."]);
        exit();
    }

    // 4. Check Duplicate Entry
    $student_id = $_SESSION['student_id'];
    
    $check_sql = "SELECT * FROM attendance WHERE student_id = ? AND date = ? AND course = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("iss", $student_id, $today, $course);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "You have already marked attendance for today!"]);
    } else {
        // 5. Insert Attendance
        $time = date("H:i:s");
        $insert_sql = "INSERT INTO attendance (student_id, course, date, time, status) VALUES (?, ?, ?, ?, 'Present')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("isss", $student_id, $course, $today, $time);
        
        if ($insert_stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Attendance Marked Successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database Error"]);
        }
    }
}
?>
