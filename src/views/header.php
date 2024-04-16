<!-- header.php -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <nav>
        <ul>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../controllers/logout.php">Kijelentkezés</a></li>
                <li><a href="home.php">Főoldal</a></li>
                <li><a href="room.php">Szobák</a></li>
            <?php else: ?>
                <li><a href="register.php">Regisztráció</a></li>
                <li><a href="login.php">Bejelentkezés</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>