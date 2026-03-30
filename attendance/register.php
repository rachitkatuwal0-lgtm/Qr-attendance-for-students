<?php
include('db.php');

if (isset($_POST['register'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $roll = $_POST['roll_no'];
    $course = $_POST['course'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO students (first_name, last_name, roll_no, course, username, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $fname, $lname, $roll, $course, $user, $pass);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: Username or Roll No already exists');</script>";
    }
}
?>
<!-- HTML Form similar to login but with more fields -->
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f0f2f5; padding-top: 50px; }</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 bg-white p-5 rounded shadow">
            <h3 class="text-center">Student Registration</h3>
            <form method="POST">
                <div class="row">
                    <div class="col"><input type="text" name="first_name" class="form-control" placeholder="First Name" required></div>
                    <div class="col"><input type="text" name="last_name" class="form-control" placeholder="Last Name" required></div>
                </div>
                <br>
                <input type="text" name="roll_no" class="form-control mb-3" placeholder="Roll Number" required>
                <select name="course" class="form-select mb-3">
                    <option value="BSc CSIT">BSc CSIT</option>
                    <option value="BCA">BCA</option>
                    <option value="BIM">BIM</option>
                </select>
                <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
            </form>
            <p class="mt-3 text-center"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</div>
</body>
</html>
