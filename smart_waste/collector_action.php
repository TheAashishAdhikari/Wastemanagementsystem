<?php
require "config.php";

$user = requireRole("Collector");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: collector.php");
    exit();
}

$action = $_POST["action"] ?? "";

if ($action === "availability") {

    $availability = $_POST["availability"];

    if (!in_array($availability, ["Available", "Unavailable"])) {
        $availability = "Unavailable";
    }

    $stmt = $pdo->prepare(
        "UPDATE users SET availability = ? WHERE id = ?"
    );

    $stmt->execute([$availability, $user["id"]]);

    addActivity($user["id"], "Updated collector availability");

    showMessage("Availability updated.");
}

if ($action === "accept") {

    $reportId = (int)$_POST["report_id"];

    $stmt = $pdo->prepare(
        "UPDATE waste_reports
         SET status = 'Accepted'
         WHERE id = ?
         AND collector_id = ?
         AND status = 'Assigned'"
    );

    $stmt->execute([$reportId, $user["id"]]);

    addActivity($user["id"], "Accepted pickup request #$reportId");

    showMessage("Pickup request accepted.");
}

if ($action === "status") {

    $reportId = (int)$_POST["report_id"];
    $status = $_POST["status"];

    $allowedStatuses = [
        "Accepted",
        "On The Way",
        "Collected",
        "Completed"
    ];

    if (in_array($status, $allowedStatuses)) {

        $stmt = $pdo->prepare(
            "UPDATE waste_reports
             SET status = ?
             WHERE id = ? AND collector_id = ?"
        );

        $stmt->execute([
            $status,
            $reportId,
            $user["id"]
        ]);

        if (in_array($status, ["Collected", "Completed"])) {

            $stmt = $pdo->prepare(
                "UPDATE waste_reports
                 SET recycling_status = 'Available'
                 WHERE id = ?
                 AND recyclable = 1
                 AND recycling_status = 'Not Listed'"
            );

            $stmt->execute([$reportId]);
        }

        addActivity(
            $user["id"],
            "Updated pickup #$reportId to $status"
        );

        showMessage("Pickup status updated.");
    }
}

if ($action === "note") {

    $reportId = (int)$_POST["report_id"];
    $note = trim($_POST["collection_note"]);

    $stmt = $pdo->prepare(
        "UPDATE waste_reports
         SET collection_note = ?
         WHERE id = ? AND collector_id = ?"
    );

    $stmt->execute([
        $note,
        $reportId,
        $user["id"]
    ]);

    addActivity($user["id"], "Added collection note to pickup #$reportId");

    showMessage("Collection note added.");
}

header("Location: collector.php");
exit();
?>