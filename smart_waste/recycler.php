<?php
require "config.php";

$user = requireRole("Recycler");

$available = $pdo->query(
    "SELECT *
     FROM waste_reports
     WHERE recyclable = 1
     AND recycling_status = 'Available'
     ORDER BY created_at DESC"
)->fetchAll();

$stmt = $pdo->prepare(
    "SELECT *
     FROM waste_reports
     WHERE recycler_id = ?
     ORDER BY created_at DESC"
);

$stmt->execute([$user["id"]]);

$records = $stmt->fetchAll();

$pageTitle = "Recycling Company";
require "header.php";
?>

<div class="page-heading">
    <div>
        <h1>Recycling Company Dashboard</h1>

        <p>
            <?php echo clean($user["company_name"] ?: $user["name"]); ?>
        </p>
    </div>
</div>

<section class="card">

    <h2>Available Recyclable Waste</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>Waste</th>
                <th>Type</th>
                <th>Location</th>
                <th>Request</th>
            </tr>

            <?php foreach ($available as $report): ?>

                <tr>

                    <td><?php echo clean($report["title"]); ?></td>

                    <td><?php echo clean($report["waste_type"]); ?></td>

                    <td><?php echo clean($report["location"]); ?></td>

                    <td>

                        <form action="recycler_action.php" method="POST">

                            <input type="hidden" name="action" value="request">
                            <input type="hidden" name="report_id" value="<?php echo $report["id"]; ?>">

                            <button type="submit">Request Collection</button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>

<section class="card">

    <h2>Manage Recycling Records</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>Waste</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Record Details</th>
            </tr>

            <?php foreach ($records as $record): ?>

                <tr>

                    <td>
                        <?php echo clean($record["title"]); ?>
                        <br>
                        <?php echo clean($record["waste_type"]); ?>
                    </td>

                    <td>
                        <?php echo clean($record["quantity_kg"] ?? "Not entered"); ?>
                    </td>

                    <td>
                        <span class="status">
                            <?php echo clean($record["recycling_status"]); ?>
                        </span>
                    </td>

                    <td>

                        <form action="recycler_action.php" method="POST">

                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="report_id" value="<?php echo $record["id"]; ?>">

                            <label>Quantity in KG</label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="quantity_kg"
                                value="<?php echo clean($record["quantity_kg"]); ?>"
                            >

                            <label>Recycling Status</label>

                            <select name="recycling_status">
                                <option>Requested</option>
                                <option>Collected</option>
                                <option>Processing</option>
                                <option>Recycled</option>
                                <option>Rejected</option>
                            </select>

                            <label>Recycling Note</label>

                            <textarea name="recycling_note"><?php
                                echo clean($record["recycling_note"]);
                            ?></textarea>

                            <button type="submit">Update Record</button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>

<?php require "footer.php"; ?>