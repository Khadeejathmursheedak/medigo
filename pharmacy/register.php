<?php
include "../includes/db.php";

$success = "";

if (isset($_POST['register'])) {
    $owner    = $_POST['owner'];
    $pname    = $_POST['pharmacy'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $location = $_POST['location'];
    $open     = $_POST['open'];
    $close    = $_POST['close'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO pharmacies
            (owner_name, pharmacy_name, email, phone, location, opening_time, closing_time, password)
            VALUES ('$owner','$pname','$email','$phone','$location','$open','$close','$password')";

    if ($conn->query($sql)) {
        $success = "✔ Pharmacy registered successfully. Wait for admin approval.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Registration | Medigo</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .time-row {
            display: flex;
            gap: 15px;
        }

        .time-row input {
            flex: 1;
        }

        .success {
            background: #e9f7ef;
            color: #198754;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        body {
    min-height: 100vh;
    background: linear-gradient(135deg, #20c997, #0d6efd);

    display: flex;
    justify-content: center;
    align-items: flex-start;   /* keeps form toward top */

    padding-top: 20px;        /* ⬅️ move form upward */
    padding-bottom: 40px;
}
.container {
    margin-top: -1px;   /* ⬅️ try 0–20px */
}


    </style>
</head>
<body>

<div class="container">

    <h2>Pharmacy Registration</h2>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">

        <input type="text" name="owner" placeholder="Owner Name" required>

        <input type="text" name="pharmacy" placeholder="Pharmacy Name" required>

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="text" name="phone" placeholder="Phone Number" required>

        <input type="text" name="location" placeholder="Location" required>

        <!-- OPEN & CLOSE TIME -->
        <div class="time-row">
            <input type="time" name="open" required>
            <input type="time" name="close" required>
        </div>

        <input type="password" name="password" placeholder="Create Password" required>

        <button type="submit" name="register">Register Pharmacy</button>

    </form>

    <div class="link">
        <a href="../login.php">← Back to Login</a>
    </div>

</div>

</body>
</html>
