<?php
require "config.php";

$user = requireRole("Collector");

$stmt = $pdo->prepare(
    "SELECT
        waste_reports.*,
        citizens.name AS citizen_name,
        citizens.phone AS citizen_phone
     FROM waste_reports
     JOIN users AS citizens
        ON citizens.id = waste_reports.citizen_id
     WHERE waste_reports.collector_id = ?
     ORDER BY waste_reports.created_at DESC"
);

$stmt->execute([$user["id"]]);

$jobs = $stmt->fetchAll();

$pageTitle = "Collector Dashboard";
require "header.php";
?>

<div class="page-heading">

    <div>
        <h1>Waste Collector Dashboard</h1>
        <p>View assigned pickups and update collection progress.</p>
    </div>

    <form action="collector_action.php" method="POST">

        <input type="hidden" name="action" value="availability">

        <select name="availability">
            <option
                value="Available"
                <?php if ($user["availability"] === "Available") echo "selected"; ?>
            >
                Available
            </option>

            <option
                value="Unavailable"
                <?php if ($user["availability"] === "Unavailable") echo "selected"; ?>
            >
                Unavailable
            </option>
        </select>

        <button type="submit">Update Availability</button>

    </form>

</div>

<div class="dashboard-grid">

<?php foreach ($jobs as $job): ?>

    <section class="card">

        <h2>
            Pickup #<?php echo $job["id"]; ?>:
            <?php echo clean($job["title"]); ?>
        </h2>

        <p><strong>Waste:</strong> <?php echo clean($job["waste_type"]); ?></p>

        <p><strong>Location:</strong> <?php echo clean($job["location"]); ?></p>

        <p><strong>Citizen:</strong> <?php echo clean($job["citizen_name"]); ?></p>

        <p><strong>Phone:</strong> <?php echo clean($job["citizen_phone"]); ?></p>

        <p>
            <strong>Status:</strong>
            <span class="status"><?php echo clean($job["status"]); ?></span>
        </p>

        <?php if ($job["status"] === "Assigned"): ?>

            <form action="collector_action.php" method="POST">

                <input type="hidden" name="action" value="accept">
                <input type="hidden" name="report_id" value="<?php echo $job["id"]; ?>">

                <button type="submit">Accept Pickup</button>

            </form>

        <?php endif; ?>

        <?php if (!in_array($job["status"], ["Completed", "Rejected"])): ?>

            <form action="collector_action.php" method="POST">

                <input type="hidden" name="action" value="status">
                <input type="hidden" name="report_id" value="<?php echo $job["id"]; ?>">

                <label>Update Pickup Status</label>

                <select name="status">
                    <option>Accepted</option>
                    <option>On The Way</option>
                    <option>Collected</option>
                    <option>Completed</option>
                </select>

                <button type="submit">Update Status</button>

            </form>

            <form action="collector_action.php" method="POST">

                <input type="hidden" name="action" value="note">
                <input type="hidden" name="report_id" value="<?php echo $job["id"]; ?>">

                <label>Collection Note</label>

                <textarea name="collection_note" required><?php
                    echo clean($job["collection_note"]);
                ?></textarea>

                <button type="submit">Add Note</button>

            </form>

        <?php endif; ?>

    </section>

<?php endforeach; ?>

</div>

<?php require "footer.php"; ?>