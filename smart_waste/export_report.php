<?php
require "config.php";

$admin = requireRole("Admin");

$reports = $pdo->query(
    "SELECT
        waste_reports.id,
        users.name AS citizen,
        waste_reports.title,
        waste_reports.waste_type,
        waste_reports.location,
        waste_reports.status,
        waste_reports.priority,
        waste_reports.recycling_status,
        waste_reports.created_at
     FROM waste_reports
     JOIN users ON users.id = waste_reports.citizen_id
     ORDER BY waste_reports.created_at DESC"
)->fetchAll();

addActivity($admin["id"], "Generated CSV waste report");

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=waste_reports.csv");

$output = fopen("php://output", "w");

fputcsv($output, [
    "ID",
    "Citizen",
    "Title",
    "Waste Type",
    "Location",
    "Pickup Status",
    "Priority",
    "Recycling Status",
    "Created"
]);

foreach ($reports as $report) {
    fputcsv($output, $report);
}

fclose($output);
exit();
?>