<?php
session_start();
if (!isset($_SESSION['student_id'])) header("Location: login.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scan QR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #000; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        #reader { width: 100%; max-width: 400px; border: 2px solid #fff; border-radius: 10px; }
        button { margin-top: 20px; padding: 10px 20px; font-size: 18px; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Scan Class QR</h2>
    <div id="reader"></div>
    <a href="student_dashboard.php" style="color:white; margin-top:20px;">Back to Dashboard</a>

    <script>
        // Audio for success beep
        const beep = new Audio('https://www.soundjay.com/button/beep-07.wav');

        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning once code is found
            html5QrcodeScanner.clear();
            beep.play();

            // Send data to PHP backend
            fetch('mark_attendance_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr_data=' + encodeURIComponent(decodedText)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    }).then(() => {
                        window.location.href = 'student_dashboard.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message
                    }).then(() => {
                        location.reload(); // Reload to scan again
                    });
                }
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 }
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>

</body>
</html>
