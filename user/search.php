<?php
session_start();
include "../includes/db.php";

// protect page
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$results = null;

if (isset($_POST['search'])) {
    $name = $_POST['medicine'] ?? '';
    $comp = $_POST['composition'] ?? '';
    $loc  = $_POST['location'] ?? '';

    $results = $conn->query("
        SELECT 
            p.pharmacy_name,
            p.phone,
            p.location,
            m.medicine_name,
            m.composition,
            m.quantity
        FROM medicines m
        JOIN pharmacies p ON m.pharmacy_id = p.id
        WHERE m.medicine_name LIKE '%$name%'
          AND m.composition LIKE '%$comp%'
          AND p.location LIKE '%$loc%'
          AND m.quantity > 0
          AND p.status = 'approved'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Medicine</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Search Medicine</h2>

    <form method="post">
        <input type="text" name="medicine" placeholder="Medicine Name">
        <input type="text" name="composition" placeholder="Composition">
        <input type="text" name="location" placeholder="Location">
        <button type="submit" name="search">Search</button>
    </form>

    <?php
    if ($results && $results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {
            echo "<div class='result'>";
            echo "<b>Pharmacy:</b> {$row['pharmacy_name']}<br>";
            echo "<b>Location:</b> {$row['location']}<br>";
            echo "<b>Medicine:</b> {$row['medicine_name']}<br>";
            echo "<b>Composition:</b> {$row['composition']}<br>";
            echo "<b>Quantity:</b> {$row['quantity']}<br>";
            echo "<b>Contact:</b> {$row['phone']}";
            echo "</div>";
        }
    } elseif ($results) {
        echo "<p style='margin-top:15px;'>No medicines found.</p>";
    }
    ?>

    <div class="link">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>

