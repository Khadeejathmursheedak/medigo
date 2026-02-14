<?php
session_start();
include "includes/db.php";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // USER CHECK
    $u = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($u->num_rows == 1) {
        $row = $u->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['role'] = 'user';
            $_SESSION['user_id'] = $row['id'];
            header("Location: user/dashboard.php");
            exit;
        }
    }

    // PHARMACY CHECK
    $p = $conn->query("SELECT * FROM pharmacies WHERE email='$email'");
    if ($p->num_rows == 1) {
        $row = $p->fetch_assoc();
        if ($row['status'] != 'approved') {
            die("Pharmacy not approved yet");
        }
        if (password_verify($password, $row['password'])) {
            $_SESSION['role'] = 'pharmacy';
            $_SESSION['pharmacy_id'] = $row['id'];
            header("Location: pharmacy/dashboard.php");
            exit;
        }
    }

    echo "Invalid login details";
}
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
<h2>Medigo Login</h2>

<form method="post">
<input name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button name="login">Login</button>
</form>

<div class="link">
<a href="user/register.php">User Register</a> |
<a href="pharmacy/register.php">Pharmacy Register</a>
</div>
</div>

