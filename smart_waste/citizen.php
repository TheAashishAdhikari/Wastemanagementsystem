<?php
require "config.php";

$user = requireRole("Citizen");

$stmt = $pdo->prepare(
    "SELECT
        waste_reports.*,
        collectors.name AS collector_name
     FROM waste_reports
     LEFT JOIN users AS collectors
        ON collectors.id = waste_reports.collector_id
     WHERE waste_reports.citizen_id = ?
     ORDER BY waste_reports.created_at DESC"
);

$stmt->execute([$user["id"]]);

$reports = $stmt->fetchAll();

$pageTitle = "Citizen Dashboard";
require "header.php";
?>

<div class="page-heading">
    <div>
        <h1>Citizen Dashboard</h1>
        <p>Welcome, <?php echo clean($user["name"]); ?></p>
    </div>
</div>

<div class="dashboard-grid">

    <section class="card">

        <h2>Report Waste</h2>

        <form
            action="citizen_action.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <input type="hidden" name="action" value="report">

            <label>Report Title</label>
            <input type="text" name="title" required>

            <label>Waste Type</label>

            <select name="waste_type" required>
                <option value="">Select Waste Type</option>
                <option>General Waste</option>
                <option>Organic Waste</option>
                <option>Plastic</option>
                <option>Paper</option>
                <option>Glass</option>
                <option>Metal</option>
                <option>Electronic Waste</option>
                <option>Hazardous Waste</option>
                <option>Construction Waste</option>
            </select>

            <label>Description</label>
            <textarea name="description" required></textarea>

            <label>Location</label>
            <input
                type="text"
                name="location"
                placeholder="Street address or location"
                required
            >

            <label>Waste Image</label>
            <input type="file" name="waste_image" accept=".jpg,.jpeg,.png">

            <label class="checkbox">
                <input type="checkbox" name="pickup_requested" value="1">
                Request waste pickup
            </label>

            <label class="checkbox">
                <input type="checkbox" name="recyclable" value="1">
                Waste contains recyclable materials
            </label>

            <button type="submit">Submit Report</button>

        </form>

    </section>

    <section class="card">

        <h2>Update Profile</h2>

        <form action="citizen_action.php" method="POST">

            <input type="hidden" name="action" value="profile">

            <label>Name</label>
            <input
                type="text"
                name="name"
                value="<?php echo clean($user["name"]); ?>"
                required
            >

            <label>Phone</label>
            <input
                type="text"
                name="phone"
                value="<?php echo clean($user["phone"]); ?>"
            >

            <label>Address</label>
            <input
                type="text"
                name="address"
                value="<?php echo clean($user["address"]); ?>"
            >

            <button type="submit">Update Profile</button>

        </form>

    </section>

</div>

<section class="card">

    <h2>Report and Pickup History</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Waste</th>
                <th>Location</th>
                <th>Collector</th>
                <th>Pickup Status</th>
                <th>Recycling</th>
            </tr>

            <?php foreach ($reports as $report): ?>

                <tr>

                    <td>#<?php echo $report["id"]; ?></td>

                    <td>
                        <?php if ($report["waste_image"]): ?>
                            <img
                                class="small-image"
                                src="uploads/<?php echo clean($report["waste_image"]); ?>"
                                alt="Waste image"
                            >
                        <?php else: ?>
                            No image
                        <?php endif; ?>
                    </td>

                    <td>
                        <strong><?php echo clean($report["title"]); ?></strong>
                        <br>
                        <?php echo clean($report["waste_type"]); ?>
                    </td>

                    <td><?php echo clean($report["location"]); ?></td>

                    <td>
                        <?php echo clean($report["collector_name"] ?? "Not assigned"); ?>
                    </td>

                    <td>
                        <span class="status">
                            <?php echo clean($report["status"]); ?>
                        </span>
                    </td>

                    <td>
                        <?php echo clean($report["recycling_status"]); ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>

<?php require "footer.php"; ?>