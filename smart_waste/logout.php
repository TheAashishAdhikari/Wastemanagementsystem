<?php
require "config.php";

if (isset($_SESSION["user_id"])) {
    addActivity($_SESSION["user_id"], "Logged out");
}

session_destroy();

header("Location: index.php");
exit();
?>