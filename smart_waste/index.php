<?php
require "config.php";

$user = currentUser();

if ($user) {
    header("Location: " . dashboardPage($user["role"]));
    exit();
}

$pageTitle = "Smart Waste Management";
require "header.php";
?>

<!-- Introduction -->
<section class="home-introduction">

    <span class="section-label">SDG 11 — SUSTAINABLE CITIES</span>

    <h1>Smart Waste Management System</h1>

    <p>
        A digital platform connecting citizens, waste collectors,
        recycling companies and administrators to manage waste efficiently.
    </p>

</section>

<!-- Login -->
<div class="login-box">

    <h2>Access Your Account</h2>

    <p>
        Login as a Citizen, Waste Collector, Recycling Company or Admin.
    </p>

    <form action="login_action.php" method="POST">

        <label for="email">Email Address</label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">Login</button>

    </form>

    <p class="register-text">
        Do not have an account?
        <a href="register.php">Register here</a>
    </p>

</div>

<!-- Moving Waste Bottle Section -->
<section class="waste-visual-section">

   <div class="bottle-container" id="scrollBottle" aria-hidden="true">

        <div class="bottle-cap"></div>

        <div class="bottle-neck"></div>

        <div class="bottle-body">
            <div id="wasteParticles"></div>
        </div>

    </div>

    <div class="waste-visual-content">

        <span class="section-label">THE WASTE PROBLEM</span>

        <h2>Turn Waste Into Valuable Resources</h2>

        <p>
            Green symbols represent recyclable materials, while red symbols
            represent waste that may enter landfill.
        </p>

        <p>
            Our platform allows citizens to report waste, collectors to
            manage pickup requests and recycling companies to recover useful
            recyclable materials.
        </p>

        <a href="register.php" class="button">
            Join the Platform
        </a>

    </div>

</section>

<!-- Statistics -->
<section class="statistics">

    <div class="stat-card">
        <strong>4</strong>
        <p>User Roles</p>
    </div>

    <div class="stat-card">
        <strong>30</strong>
        <p>Use Cases</p>
    </div>

    <div class="stat-card">
        <strong>1</strong>
        <p>Connected Platform</p>
    </div>

    <div class="stat-card">
        <strong>11</strong>
        <p>SDG Goal</p>
    </div>

</section>

<!-- System Users -->
<section class="user-section">

    <div class="section-heading">
        <div>
            <span class="section-label">CONNECTED USERS</span>
            <h2>One Platform, Four User Roles</h2>
        </div>
    </div>

    <div class="dashboard-grid">

        <div class="card">
            <h3>Citizen</h3>
            <p>
                Report waste issues, upload images, request pickups and
                track pickup progress.
            </p>
        </div>

        <div class="card">
            <h3>Waste Collector</h3>
            <p>
                View assigned pickups, accept jobs, add notes and update
                collection status.
            </p>
        </div>

        <div class="card">
            <h3>Recycling Company</h3>
            <p>
                View recyclable waste listings, request collections and
                maintain recycling records.
            </p>
        </div>

        <div class="card">
            <h3>Administrator</h3>
            <p>
                Approve accounts, manage users, review reports and monitor
                system activities.
            </p>
        </div>

    </div>

</section>

<!-- Moving particle JavaScript -->
<script>
const particleContainer = document.getElementById("wasteParticles");

if (particleContainer) {
    for (let i = 0; i < 120; i++) {
        const particle = document.createElement("span");
        particle.className = "waste-particle";

        const x = Math.random() * 88 + 6;
        const y = Math.random() * 90 + 5;
        const size = Math.random() * 7 + 8;

        particle.style.left = x + "%";
        particle.style.top = y + "%";
        particle.style.width = size + "px";
        particle.style.height = size + "px";

        if (y > 76) {
            particle.classList.add("red-particle");
        } else {
            particle.classList.add("green-particle");
        }

        particle.style.setProperty(
            "--move-x",
            (Math.random() * 20 - 10) + "px"
        );

        particle.style.setProperty(
            "--move-y",
            (Math.random() * 14 + 8) + "px"
        );

        particle.style.setProperty(
            "--duration",
            (3 + Math.random() * 4) + "s"
        );

        particle.style.setProperty(
            "--delay",
            (-Math.random() * 6) + "s"
        );

        particleContainer.appendChild(particle);
    }
}
</script>

<script>
const scrollBottle = document.getElementById("scrollBottle");
const wasteSection = document.querySelector(".waste-visual-section");

function moveBottleOnScroll() {
    if (!scrollBottle || !wasteSection) {
        return;
    }

    const sectionPosition = wasteSection.getBoundingClientRect();
    const screenHeight = window.innerHeight;

    let progress =
        (screenHeight - sectionPosition.top) /
        (screenHeight + sectionPosition.height);

    progress = Math.max(0, Math.min(1, progress));

    // Bottle moves from 60px down to 60px up
    const moveY = 60 - progress * 120;

    // Bottle rotates slightly
    const rotation = -3 + progress * 6;

    // Bottle becomes slightly larger
    const scale = 0.96 + progress * 0.08;

    scrollBottle.style.transform =
        "translateY(" + moveY + "px) " +
        "rotate(" + rotation + "deg) " +
        "scale(" + scale + ")";
}

let scrolling = false;

window.addEventListener("scroll", function () {
    if (!scrolling) {
        window.requestAnimationFrame(function () {
            moveBottleOnScroll();
            scrolling = false;
        });

        scrolling = true;
    }
});

moveBottleOnScroll();
</script>

<?php require "footer.php"; ?>