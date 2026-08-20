<?php
$user = currentUser();
$pageTitle = $pageTitle ?? "Smart Waste Management System";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo clean($pageTitle); ?></title>

   <link rel="stylesheet" href="style.css?v=10">
</head>

<body>

<header>
    <a class="logo" href="index.php">
        ♻ Smart Waste Management
    </a>

    <nav>
        <?php if ($user): ?>

            <?php if ($user["role"] === "Citizen"): ?>
                <a href="citizen.php">Citizen Dashboard</a>

            <?php elseif ($user["role"] === "Collector"): ?>
                <a href="collector.php">Collector Dashboard</a>

            <?php elseif ($user["role"] === "Recycler"): ?>
                <a href="recycler.php">Recycler Dashboard</a>

            <?php elseif ($user["role"] === "Admin"): ?>
                <a href="admin.php">Admin Dashboard</a>
            <?php endif; ?>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="index.php">Login</a>
            <a href="register.php">Register</a>

        <?php endif; ?>
    </nav>
</header>

<main class="container">

<?php
if (isset($_SESSION["message"])) {
    echo '<div class="message">' . clean($_SESSION["message"]) . '</div>';
    unset($_SESSION["message"]);
}
?>