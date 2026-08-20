<?php
require "config.php";

$user = requireRole("Admin");

$totalUsers = $pdo->query(
    "SELECT COUNT(*) FROM users"
)->fetchColumn();

$totalReports = $pdo->query(
    "SELECT COUNT(*) FROM waste_reports"
)->fetchColumn();

$totalPickups = $pdo->query(
    "SELECT COUNT(*) FROM waste_reports
     WHERE pickup_requested = 1"
)->fetchColumn();

$totalRecycled = $pdo->query(
    "SELECT COUNT(*) FROM waste_reports
     WHERE recycling_status = 'Recycled'"
)->fetchColumn();

$users = $pdo->query(
    "SELECT * FROM users ORDER BY created_at DESC"
)->fetchAll();

$collectors = $pdo->query(
    "SELECT * FROM users
     WHERE role = 'Collector'
     ORDER BY created_at DESC"
)->fetchAll();

$activeCollectors = $pdo->query(
    "SELECT * FROM users
     WHERE role = 'Collector'
     AND status = 'Active'
     ORDER BY name"
)->fetchAll();

$reports = $pdo->query(
    "SELECT
        waste_reports.*,
        citizens.name AS citizen_name,
        collectors.name AS collector_name
     FROM waste_reports
     JOIN users AS citizens
        ON citizens.id = waste_reports.citizen_id
     LEFT JOIN users AS collectors
        ON collectors.id = waste_reports.collector_id
     ORDER BY waste_reports.created_at DESC"
)->fetchAll();

$activities = $pdo->query(
    "SELECT
        activities.*,
        users.name
     FROM activities
     LEFT JOIN users ON users.id = activities.user_id
     ORDER BY activities.created_at DESC
     LIMIT 50"
)->fetchAll();

$pageTitle = "Admin Dashboard";
require "header.php";
?>

<h1>Administrator Dashboard</h1>

<div class="statistics">

    <div class="stat-card">
        <h3>Total Users</h3>
        <strong><?php echo $totalUsers; ?></strong>
    </div>

    <div class="stat-card">
        <h3>Waste Reports</h3>
        <strong><?php echo $totalReports; ?></strong>
    </div>

    <div class="stat-card">
        <h3>Pickup Requests</h3>
        <strong><?php echo $totalPickups; ?></strong>
    </div>

    <div class="stat-card">
        <h3>Recycled Records</h3>
        <strong><?php echo $totalRecycled; ?></strong>
    </div>

</div>

<section class="card">

    <div class="section-heading">
        <h2>Generate Reports</h2>

        <a class="button" href="export_report.php">
            Export CSV
        </a>
    </div>

</section>

<section class="card">

    <h2>Approve Collector Accounts</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>Collector</th>
                <th>Email</th>
                <th>Status</th>
                <th>Availability</th>
                <th>Action</th>
            </tr>

            <?php foreach ($collectors as $collector): ?>

                <tr>

                    <td><?php echo clean($collector["name"]); ?></td>

                    <td><?php echo clean($collector["email"]); ?></td>

                    <td><?php echo clean($collector["status"]); ?></td>

                    <td><?php echo clean($collector["availability"]); ?></td>

                    <td>

                        <form action="admin_action.php" method="POST">

                            <input type="hidden" name="action" value="approve_collector">
                            <input type="hidden" name="user_id" value="<?php echo $collector["id"]; ?>">

                            <select name="status">
                                <option value="Active">Approve</option>
                                <option value="Suspended">Suspend</option>
                            </select>

                            <button type="submit">Save</button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>

<section class="card">

    <h2>Manage Users</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($users as $account): ?>

                <tr>

                    <td><?php echo clean($account["name"]); ?></td>

                    <td><?php echo clean($account["role"]); ?></td>

                    <td><?php echo clean($account["email"]); ?></td>

                    <td><?php echo clean($account["status"]); ?></td>

                    <td>

                        <?php if ($account["id"] != $user["id"]): ?>

                            <form action="admin_action.php" method="POST">

                                <input type="hidden" name="action" value="user_status">
                                <input type="hidden" name="user_id" value="<?php echo $account["id"]; ?>">

                                <select name="status">
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Suspended</option>
                                </select>

                                <button type="submit">Update</button>

                            </form>

                        <?php else: ?>

                            Current Admin

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>

<section class="card">

    <h2>Manage Waste Reports</h2>

    <?php foreach ($reports as $report): ?>

        <div class="admin-report">

            <h3>
                #<?php echo $report["id"]; ?>:
                <?php echo clean($report["title"]); ?>
            </h3>

            <p>
                <strong>Citizen:</strong>
                <?php echo clean($report["citizen_name"]); ?>
            </p>

            <p>
                <strong>Location:</strong>
                <?php echo clean($report["location"]); ?>
            </p>

            <form action="admin_action.php" method="POST">

                <input type="hidden" name="action" value="report">
                <input type="hidden" name="report_id" value="<?php echo $report["id"]; ?>">

                <div class="form-grid">

                    <div>
                        <label>Assign Collector</label>

                        <select name="collector_id">

                            <option value="0">Not Assigned</option>

                            <?php foreach ($activeCollectors as $collector): ?>

                                <option
                                    value="<?php echo $collector["id"]; ?>"
                                    <?php
                                    if ($report["collector_id"] == $collector["id"]) {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    <?php echo clean($collector["name"]); ?>
                                    - <?php echo clean($collector["availability"]); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label>Status</label>

                        <select name="status">

                            <?php
                            $statuses = [
                                "Reported",
                                "Assigned",
                                "Accepted",
                                "On The Way",
                                "Collected",
                                "Completed",
                                "Rejected"
                            ];

                            foreach ($statuses as $status):
                            ?>

                                <option
                                    value="<?php echo $status; ?>"
                                    <?php
                                    if ($report["status"] === $status) {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    <?php echo $status; ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label>Priority</label>

                        <select name="priority">

                            <?php
                            foreach (["Low", "Medium", "High", "Urgent"] as $priority):
                            ?>

                                <option
                                    value="<?php echo $priority; ?>"
                                    <?php
                                    if ($report["priority"] === $priority) {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    <?php echo $priority; ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label class="checkbox">

                            <input
                                type="checkbox"
                                name="recyclable"
                                value="1"
                                <?php
                                if ($report["recyclable"] == 1) {
                                    echo "checked";
                                }
                                ?>
                            >

                            Recyclable Waste

                        </label>
                    </div>

                </div>

                <button type="submit">Update Report</button>

            </form>

        </div>

    <?php endforeach; ?>

</section>

<section class="card">

    <h2>Monitor System Activities</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>User</th>
                <th>Activity</th>
                <th>Date</th>
            </tr>

            <?php foreach ($activities as $activity): ?>

                <tr>

                    <td>
                        <?php echo clean($activity["name"] ?? "System"); ?>
                    </td>

                    <td>
                        <?php echo clean($activity["activity"]); ?>
                    </td>

                    <td>
                        <?php echo clean($activity["created_at"]); ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</section>

<?php require "footer.php"; ?>