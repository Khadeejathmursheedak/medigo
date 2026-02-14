<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pharmacy') {
    header("Location: ../login.php");
    exit;
}

$pid = $_SESSION['pharmacy_id'];

/* Dashboard Data */
$total = $conn->query("SELECT COUNT(*) as count FROM medicines WHERE pharmacy_id='$pid'")->fetch_assoc()['count'];
$low = $conn->query("SELECT COUNT(*) as count FROM medicines WHERE pharmacy_id='$pid' AND quantity < 10")->fetch_assoc()['count'];
$expired = $conn->query("SELECT COUNT(*) as count FROM medicines WHERE pharmacy_id='$pid' AND expiry_date < CURDATE()")->fetch_assoc()['count'];
$value = $conn->query("SELECT SUM(quantity * price) as total FROM medicines WHERE pharmacy_id='$pid'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medigo - Pharmacy Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
        }

        /* HEADER */
        .header {
            background: linear-gradient(90deg, #0d6efd, #20c997);
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            font-size: 20px;
        }

        .header a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            background: rgba(255,255,255,0.2);
            padding: 8px 14px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .header a:hover {
            background: rgba(255,255,255,0.35);
        }

        /* MAIN CONTENT */
        .container {
            padding: 40px;
        }

        h1 {
            margin-bottom: 25px;
            color: #ffffffff;
        }

        /* STAT CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            padding: 25px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-card p {
            font-size: 22px;
            font-weight: bold;
        }

        .blue { background: #0d6efd; }
        .green { background: #20c997; }
        .orange { background: #fd7e14; }
        .red { background: #dc3545; }

        /* ACTION BUTTONS */
        

        .actions {
    display: flex;
    justify-content: center;   /* centers horizontally */
    align-items: center;
    gap: 25px;
    margin-top: 20px;
}
        .actions a {
    text-decoration: none;
    padding: 14px 35px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    font-size: 15px;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.add-btn {
    background: linear-gradient(90deg, #20c997, #17a589);
}

.view-btn {
    background: linear-gradient(90deg, #0d6efd, #0b5ed7);
}

.actions a:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}


        

        

    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <h1>MEDIGO - Pharmacy Panel</h1>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- MAIN -->
    <div class="container">

        

        <!-- STATISTICS -->
        <div class="cards">

            <div class="stat-card blue">
                <h3>Total Medicines</h3>
                <p><?= $total ?></p>
            </div>

            <div class="stat-card green">
                <h3>Total Stock Value</h3>
                <p>₹ <?= number_format($value ?? 0, 2) ?></p>
            </div>

            <div class="stat-card orange">
                <h3>Low Stock</h3>
                <p><?= $low ?></p>
            </div>

            <div class="stat-card red">
                <h3>Expired</h3>
                <p><?= $expired ?></p>
            </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="actions">
            <a href="add_medicine.php" class="add-btn">➕ Add Medicine</a>
            <a href="view_medicine.php" class="view-btn">💊 View Medicines</a>
        </div>

    </div>

</body>
</html>
