<?php
session_start();
include('db.php');
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- QR Generator Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <style>
        .qr-box { background: white; padding: 20px; text-align: center; border-radius: 10px; margin-bottom: 20px; }
        canvas { border: 10px solid #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark p-3">
    <a class="navbar-brand" href="#">Admin Panel</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">
    <div class="row">
        <!-- QR Generator Section -->
        <div class="col-md-4">
            <div class="qr-box shadow">
                <h4>Generate Class QR</h4>
                <p class="text-muted">Select course to generate unique code</p>
                <select id="courseSelect" class="form-select mb-3">
                    <option value="BSc CSIT">BSc CSIT</option>
                    <option value="BCA">BCA</option>
                    <option value="BIM">BIM</option>
                </select>
                <button onclick="generateQR()" class="btn btn-primary w-100 mb-3">Generate QR</button>
                
                <canvas id="qr-code"></canvas>
                <p id="qr-text" class="mt-2 text-success fw-bold"></p>
            </div>
        </div>

        <!-- Attendance Report Section -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    Today's Attendance (<?php echo date('Y-m-d'); ?>)
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Roll No</th>
                                <th>Course</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $today = date('Y-m-d');
                            $sql = "SELECT s.first_name, s.last_name, s.roll_no, a.course, a.time 
                                    FROM attendance a 
                                    JOIN students s ON a.student_id = s.id 
                                    WHERE a.date = '$today' ORDER BY a.time DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['first_name']} {$row['last_name']}</td>
                                            <td>{$row['roll_no']}</td>
                                            <td>{$row['course']}</td>
                                            <td>{$row['time']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No attendance marked yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function generateQR() {
        const course = document.getElementById('courseSelect').value;
        const date = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
        // Create a secure token structure
        const qrData = JSON.stringify({
            course: course,
            date: date,
            secret: "my_secure_college_token" // Validates on server
        });

        const qr = new QRious({
            element: document.getElementById('qr-code'),
            value: qrData,
            size: 250,
            level: 'H'
        });

        document.getElementById('qr-text').innerText = "Active for: " + course;
    }
</script>

</body>
</html>
