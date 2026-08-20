<?php
require "config.php";

$pageTitle = "Register Account";
require "header.php";
?>

<div class="form-box">

    <h1>Register Account</h1>

    <form action="register_action.php" method="POST">

        <div class="form-grid">

            <div>
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div>
                <label>Phone</label>
                <input type="text" name="phone">
            </div>

            <div>
                <label>Address</label>
                <input type="text" name="address">
            </div>

            <div>
                <label>Account Type</label>

                <select name="role" required>
                    <option value="Citizen">Citizen</option>
                    <option value="Collector">Waste Collector</option>
                    <option value="Recycler">Recycling Company</option>
                </select>
            </div>

            <div>
                <label>Company Name</label>
                <input
                    type="text"
                    name="company_name"
                    placeholder="Only for recycling company"
                >
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" minlength="8" required>
            </div>

            <div>
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" minlength="8" required>
            </div>

        </div>

        <button type="submit">Register</button>

    </form>

</div>

<?php require "footer.php"; ?>