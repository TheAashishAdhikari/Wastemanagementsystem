<?php
require "config.php";

$admin = requireRole("Admin");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit();
}

$action = $_POST["action"] ?? "";

if ($action === "approve_collector") {

    $userId = (int)$_POST["user_id"];
    $status = $_POST["status"];

    if (!in_array($status, ["Active", "Suspended"])) {
        $status = "Pending";
    }

    $stmt = $pdo->prepare(
        "UPDATE users
         SET status = ?
         WHERE id = ? AND role = 'Collector'"
    );

    $stmt->execute([$status, $userId]);

    addActivity(
        $admin["id"],
        "Changed collector #$userId status to $status"
    );

    showMessage("Collector account updated.");
}

if ($action === "user_status") {

    $userId = (int)$_POST["user_id"];
    $status = $_POST["status"];

    if (
        $userId !== $admin["id"] &&
        in_array($status, ["Active", "Pending", "Suspended"])
    ) {
        $stmt = $pdo->prepare(
            "UPDATE users SET status = ? WHERE id = ?"
        );

        $stmt->execute([$status, $userId]);

        addActivity(
            $admin["id"],
            "Updated user #$userId status to $status"
        );

        showMessage("User account updated.");
    }
}

if ($action === "report") {

    $reportId = (int)$_POST["report_id"];
    $collectorId = (int)$_POST["collector_id"];
    $status = $_POST["status"];
    $priority = $_POST["priority"];
    $recyclable = isset($_POST["recyclable"]) ? 1 : 0;

    if ($collectorId === 0) {
        $collectorId = null;
    }

    if ($collectorId && $status === "Reported") {
        $status = "Assigned";
    }

    $stmt = $pdo->prepare(
        "UPDATE waste_reports
         SET collector_id = ?,
             status = ?,
             priority = ?,
             recyclable = ?
         WHERE id = ?"
    );

    $stmt->execute([
        $collectorId,
        $status,
        $priority,
        $recyclable,
        $reportId
    ]);

    if (
        $recyclable === 1 &&
        in_array($status, ["Collected", "Completed"])
    ) {
        $stmt = $pdo->prepare(
            "UPDATE waste_reports
             SET recycling_status = 'Available'
             WHERE id = ?
             AND recycling_status = 'Not Listed'"
        );

        $stmt->execute([$reportId]);
    }

    addActivity(
        $admin["id"],
        "Managed waste report #$reportId"
    );

    showMessage("Waste report updated.");
}

header("Location: admin.php");
exit();
?>