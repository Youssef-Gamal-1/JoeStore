<header>
    <div class="container">
    <div class="toggle-menu"><i class="fa-solid fa-bars"></i></div>
        <nav class="lower-nav">
           <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="items.php">Items</a></li>
                <li><a href="members.php">Members</a></li>
                <li><a href="comments.php">Comments</a></li>
                <li><a href="payments.php">Payments</a></li>
            </ul>
        </nav>
        <div class="acc-menu">
            <div class="toggle">
                <p class="name"><?= $_SESSION['Username'] ?></p>
                <i class="fa-solid fa-angle-down"></i>
            </div>
            <ul class="edit">
                <li><a href="../home.php" target="blank"><i class="fa-solid fa-shop"></i>Visit Shop</a></li>
                <li><a href="members.php?do=Edit&id=<?= $_SESSION['Id']; ?>"><i class="fa-regular fa-user"></i>Edit Profile</a></li>
                <li><a href="#Settings"><i class="fa-solid fa-gears"></i>Settings</a></li>
                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>
