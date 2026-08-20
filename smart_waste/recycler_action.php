<?php
require "config.php";

$user = requireRole("Recycler");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: recycler.php");
    exit();
}

$action = $_POST["action"] ?? "";

if ($action === "request") {

    $reportId = (int)$_POST["report_id"];

    $stmt = $pdo->prepare(
        "UPDATE waste_reports
         SET recycler_id = ?,
             recycling_status = 'Requested'
         WHERE id = ?
         AND recyclable = 1
         AND recycling_status = 'Available'"
    );

    $stmt->execute([
        $user["id"],
        $reportId
    ]);

    if ($stmt->rowCount() > 0) {

        addActivity(
            $user["id"],
            "Requested recyclable waste collection #$reportId"
        );

        showMessage("Recycling collection requested.");

    } else {
        showMessage("This recycling listing is no longer available.");
    }
}

if ($action === "update") {

    $reportId = (int)$_POST["report_id"];
    $quantity = $_POST["quantity_kg"];
    $status = $_POST["recycling_status"];
    $note = trim($_POST["recycling_note"]);

    $allowedStatuses = [
        "Requested",
        "Collected",
        "Processing",
        "Recycled",
        "Rejected"
    ];

    if (!in_array($status, $allowedStatuses)) {
        showMessage("Invalid recycling status.");
        header("Location: recycler.php");
        exit();
    }

    $stmt = $pdo->prepare(
        "UPDATE waste_reports
         SET quantity_kg = ?,
             recycling_status = ?,
             recycling_note = ?
         WHERE id = ? AND recycler_id = ?"
    );

    $stmt->execute([
        $quantity ?: null,
        $status,
        $note,
        $reportId,
        $user["id"]
    ]);

    addActivity(
        $user["id"],
        "Updated recycling record #$reportId to $status"
    );

    showMessage("Recycling record updated.");
}

header("Location: recycler.php");
exit();
?>