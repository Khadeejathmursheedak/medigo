<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['pharmacy_id'])) {
    header("Location: ../login.php");
    exit;
}

$pid = $_SESSION['pharmacy_id'];

/* Increase quantity */
if (isset($_POST['increase'])) {
    $mid = (int)$_POST['medicine_id'];
    $amount = (int)$_POST['amount'];

    if ($amount > 0) {
        $update = $conn->prepare("
            UPDATE medicines 
            SET quantity = quantity + ? 
            WHERE id = ? 
            AND pharmacy_id = ?
        ");
        $update->bind_param("iii", $amount, $mid, $pid);
        $update->execute();
    }
}

/* Decrease quantity */
if (isset($_POST['decrease'])) {
    $mid = (int)$_POST['medicine_id'];
    $amount = (int)$_POST['amount'];

    if ($amount > 0) {
        $update = $conn->prepare("
            UPDATE medicines 
            SET quantity = quantity - ? 
            WHERE id = ? 
            AND pharmacy_id = ?
            AND quantity >= ?
        ");
        $update->bind_param("iiii", $amount, $mid, $pid, $amount);
        $update->execute();
    }
}

/* Fetch medicines */
$stmt = $conn->prepare("SELECT * FROM medicines WHERE pharmacy_id = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Medicines - Medigo</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
            padding: 40px;
        }

        h2 {
            margin-bottom: 20px;
            color: #0d6efd;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(90deg, #0d6efd, #20c997);
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background: #f1fdf9;
        }

        .qty {
            font-weight: 600;
            color: #20c997;
        }

        .low-stock {
            color: red;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 15px;
            background: #0d6efd;
            color: white;
            border-radius: 6px;
        }

        .back:hover {
            background: #0b5ed7;
        }

        /* BUTTONS */
        .btn {
            border: none;
            padding: 5px 9px;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }

        .plus {
            background: #20c997;
        }

        .plus:hover {
            background: #17a589;
        }

        .minus {
            background: #dc3545;
        }

        .minus:hover {
            background: #bb2d3b;
        }
    </style>
</head>

<body>

<h2>💊 Your Medicines</h2>

<div class="card">

<?php if ($result->num_rows > 0) { ?>

<table>
    <tr>
        <th>Medicine Name</th>
        <th>Composition</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Expiry</th>
        <th>Action</th>
    </tr>

    <?php while ($m = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($m['medicine_name']) ?></td>
            <td><?= htmlspecialchars($m['composition']) ?></td>

            <td class="qty <?= ($m['quantity'] < 10) ? 'low-stock' : '' ?>">
                <?= $m['quantity'] ?>
            </td>

            <td>₹ <?= $m['price'] ?></td>
            <td><?= $m['expiry_date'] ?></td>

            <td>
                <form method="post" style="display:flex; gap:6px; align-items:center;">
                    <input type="hidden" name="medicine_id" value="<?= $m['id'] ?>">
                    
                    <input type="number" name="amount" min="1" value="1" style="width:60px;">

                    <button type="submit" name="increase" class="btn plus">+</button>
                    <button type="submit" name="decrease" class="btn minus">−</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<?php } else { ?>
    <div class="empty">No medicines added yet.</div>
<?php } ?>

</div>

<a class="back" href="dashboard.php">← Back to Dashboard</a>

</body>
</html>
