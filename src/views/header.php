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
            <?php 
                if ($conn && is_admin($conn, $_SESSION['user_id'])) {
                    echo '<li><a href="./admin.php">Adminisztráció</a></li>';
                    echo '<li><a href="./new_question.php">Kérdések kezelése</a></li>';
                }
            ?>
            <!-- Új menüpontok az adott quiz kitöltése és az eredmények megtekintése számára -->
            <?php if (isset($_SESSION['room_id'])): ?>
                <li><a href="quiz.php">Quiz Kitöltése</a></li>
                <li><a href="quiz_toplist.php">Szoba Eredményei</a></li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="register.php">Regisztráció</a></li>
            <li><a href="login.php">Bejelentkezés</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>
