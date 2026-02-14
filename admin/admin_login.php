<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medigo Admin Login</title>
        <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #0d6efd, #20c997);
}

.login-box {
    background: #ffffff;
    width: 370px;
    padding: 45px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.login-box h2 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
    color: #0d6efd;
}

.login-box input {
    width: 100%;
    padding: 13px 15px;
    margin-bottom: 20px;
    border: 1px solid #dcdcdc;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.3s ease;
}

.login-box input:focus {
    border-color: #20c997;
    outline: none;
    box-shadow: 0 0 0 3px rgba(32, 201, 151, 0.15);
}

.login-box button {
    width: 100%;
    padding: 13px;
    background: linear-gradient(90deg, #0d6efd, #20c997);
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s ease;
}

.login-box button:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.error {
    background: #e9f7ef;
    color: #198754;
    padding: 9px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
    text-align: center;
}
</style>

</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
