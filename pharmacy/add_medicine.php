<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pharmacy') {
    header("Location: ../login.php");
    exit;
}

$success = "";

if (isset($_POST['add'])) {

    $pid     = $_SESSION['pharmacy_id'];
    $name    = $_POST['name'];
    $comp    = $_POST['composition'];
    $dosage  = $_POST['dosage'];
    $type    = $_POST['type'];
    $qty     = $_POST['quantity'];
    $price   = $_POST['price'];
    $expiry  = $_POST['expiry'];

    $stmt = $conn->prepare("INSERT INTO medicines 
    (pharmacy_id, medicine_name, composition, dosage, medicine_type, quantity, price, expiry_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("issssids", $pid, $name, $comp, $dosage, $type, $qty, $price, $expiry);
    $stmt->execute();

    $success = "Medicine added successfully!";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine - Medigo</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0d6efd;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #20c997;
            outline: none;
            box-shadow: 0 0 0 3px rgba(32,201,151,0.15);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #0d6efd, #20c997);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .success {
            background: #e9f7ef;
            color: #198754;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #0d6efd;
            font-size: 14px;
        }

        .back:hover {
            text-decoration: underline;
        }

        select {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: 0.3s;
}

select:focus {
    border-color: #20c997;
    outline: none;
    box-shadow: 0 0 0 3px rgba(32,201,151,0.15);
}

    </style>
</head>

<body>

<div class="card">

    <h2>➕ Add Medicine</h2>

    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="post">
    <input type="text" name="name" placeholder="Medicine Name" required>
    <input type="text" name="composition" placeholder="Composition" required>
    <input type="text" name="dosage" placeholder="Dosage (e.g. 500mg)" required>

    <select name="type" required>
        <option value="">Select Medicine Type</option>
        <option>Tablet</option>
        <option>Capsule</option>
        <option>Syrup</option>
        <option>Injection</option>
        <option>Other</option>
    </select>

    <input type="number" name="quantity" placeholder="Quantity" required>
    <input type="number" step="0.01" name="price" placeholder="Price (₹)" required>
    <input type="date" name="expiry" required>

    <button name="add">Add Medicine</button>
    </form>


    <a class="back" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>
