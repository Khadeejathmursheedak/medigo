<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

/* Dashboard Statistics */
$users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$pharmacies = $conn->query("SELECT COUNT(*) as count FROM pharmacies")->fetch_assoc()['count'];
$medicines = $conn->query("SELECT COUNT(*) as count FROM medicines")->fetch_assoc()['count'];
$reports = $conn->query("SELECT COUNT(*) as count FROM reports WHERE status='pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medigo - Admin Dashboard</title>

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
            text-decoration: none;
            color: white;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .header a:hover {
            background: rgba(255,255,255,0.35);
        }

        /* MAIN */
        .container {
            padding: 40px;
        }

        h1 {
            margin-bottom: 30px;
            color: #0d6efd;
        }

        /* STAT CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            padding: 25px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .card p {
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
            justify-content: center;
            gap: 25px;
        }

        .actions a {
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .manage-users { background: #0d6efd; }
        .manage-pharmacy { background: #20c997; }
        .view-reports { background: #dc3545; }

        .actions a:hover {
            transform: translateY(-3px);
            opacity: 0.9;
        }

    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <h2>MEDIGO - Admin Panel</h2>
        <a href="admin_logout.php">Logout</a>
    </div>

    <!-- MAIN -->
    <div class="container">

        <h1>Dashboard</h1>

        <!-- STATISTICS -->
        <div class="cards">
            <div class="card blue">
                <h3>Total Users</h3>
                <p><?= $users ?></p>
            </div>

            <div class="card green">
                <h3>Total Pharmacies</h3>
                <p><?= $pharmacies ?></p>
            </div>

           

            <div class="card red">
                <h3>Pending Reports</h3>
                <p><?= $reports ?></p>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="actions">
            <a href="manage_users.php" class="manage-users">Manage Users</a>
            <a href="manage_pharmacy.php" class="manage-pharmacy">Manage Pharmacies</a>
            <a href="view_reports.php" class="view-reports">View Reports</a>
        </div>

    </div>

</body>
</html>

