<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$results = null;
$medicine = $composition = $location = "";

/* ================= SEARCH ================= */
if (isset($_POST['search'])) {

    $medicine = trim($_POST['medicine'] ?? "");
    $composition = trim($_POST['composition'] ?? "");
    $location = trim($_POST['location'] ?? "");

    $sql = "
        SELECT 
            p.id AS pharmacy_id,
            p.pharmacy_name,
            p.location,
            p.phone,
            m.medicine_name,
            m.composition,
            m.quantity
        FROM medicines m
        JOIN pharmacies p ON m.pharmacy_id = p.id
        WHERE m.medicine_name LIKE ?
          AND m.composition LIKE ?
          AND p.location LIKE ?
          AND m.quantity > 0
          AND p.status = 'approved'
    ";

    $stmt = $conn->prepare($sql);

    /* ✅ FIX: define variables first */
    $likeMedicine    = "%$medicine%";
    $likeComposition = "%$composition%";
    $likeLocation    = "%$location%";

    /* ✅ FIX: bind variables only */
    $stmt->bind_param("sss", $likeMedicine, $likeComposition, $likeLocation);

    $stmt->execute();
    $results = $stmt->get_result();
}

/* ================= REPORT TO ADMIN ================= */
if (isset($_POST['report'])) {

    $user_id     = $_SESSION['user_id'];
    $pharmacy_id = (int) $_POST['pharmacy_id'];
    $description = trim($_POST['message']);

    if ($description !== "") {

        $stmt = $conn->prepare("
            INSERT INTO reports
            (reported_by, user_id, pharmacy_id, report_type, description)
            VALUES (?, ?, ?, ?, ?)
        ");

        $reported_by = 'user';
        $report_type = 'medicine_issue';

        $stmt->bind_param(
            "siiss",
            $reported_by,
            $user_id,
            $pharmacy_id,
            $report_type,
            $description
        );

        $stmt->execute();
        $report_success = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard | Medigo</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body { background: #f4f6f9; }

        .header {
            background: linear-gradient(90deg, #0d6efd, #20c997);
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 8px 14px;
            border-radius: 6px;
        }

        .container { padding: 40px; }

        .search-box, .results {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }

        .search-grid input {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(90deg, #0d6efd, #0b5ed7);
            border: none;
            color: white;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        .result-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .result-item:last-child { border-bottom: none; }

        .result-item strong {
            color: #0d6efd;
            font-size: 16px;
        }

        .report-btn {
            margin-top: 8px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn {
            margin-top: 6px;
            background: #0d6efd;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .report-form { display: none; margin-top: 10px; }

        .success { color: green; margin-bottom: 15px; }
        .no-result { color: #888; }
    </style>
</head>
<body>

<div class="header">
    <h2>Medigo – User Dashboard</h2>
    <a href="../logout.php">Logout</a>
</div>

<div class="container">

    <h1>Search Medicines</h1>

    <div class="search-box">
        <form method="post">
            <div class="search-grid">
                <input type="text" name="medicine" placeholder="Medicine Name" value="<?= htmlspecialchars($medicine) ?>">
                <input type="text" name="composition" placeholder="Composition" value="<?= htmlspecialchars($composition) ?>">
                <input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($location) ?>">
            </div>
            <button type="submit" name="search">Search</button>
        </form>
    </div>

    <?php if (!empty($report_success)): ?>
        <p class="success">✔ Report submitted successfully</p>
    <?php endif; ?>

    <?php if ($results !== null): ?>
        <div class="results">
            <h2>Search Results</h2>

            <?php if ($results->num_rows > 0): ?>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <div class="result-item">
                        <strong><?= htmlspecialchars($row['medicine_name']) ?></strong>
                        (<?= htmlspecialchars($row['composition']) ?>)<br>

                        Pharmacy: <?= htmlspecialchars($row['pharmacy_name']) ?> |
                        Location: <?= htmlspecialchars($row['location']) ?><br>

                        Available: <?= (int)$row['quantity'] ?> |
                        Contact: <?= htmlspecialchars($row['phone']) ?><br>

                        <button type="button" class="report-btn" onclick="toggleReport(this)">
                            Report to Admin
                        </button>

                        <form method="post" class="report-form">
                            <input type="hidden" name="pharmacy_id" value="<?= (int)$row['pharmacy_id'] ?>">
                            <textarea name="message" required
                                      placeholder="Describe the issue..."
                                      style="width:100%; padding:8px;"></textarea>
                            <button type="submit" name="report" class="submit-btn">
                                Submit Report
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-result">No medicines found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<script>
function toggleReport(btn) {
    const form = btn.nextElementSibling;
    if (form.style.display === "block") {
        form.style.display = "none";
        btn.textContent = "Report to Admin";
    } else {
        form.style.display = "block";
        btn.textContent = "Cancel Report";
    }
}
</script>

</body>
</html>
