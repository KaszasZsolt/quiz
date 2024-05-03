<!-- header.php -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS fájl hivatkozása -->
    <link rel="stylesheet" href="../../public_html/css/style.css">
    <link rel="stylesheet" href="../../public_html/css/header.css">

</head>
<body>

<header>
    <nav>
        <ul>
            
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="home.php">Főoldal</a></li>
            <li><a href="room.php">Szobák</a></li>
            <?php 
                if ($conn && is_admin($conn, $_SESSION['user_id'])) {
                    echo '<li><a href="./admin.php">Adminisztráció</a></li>';
                    echo '<li><a href="./new_question.php">Kérdések kezelése</a></li>';
                    echo '<li><a href="./quiz_tema.php">Quiz témák kezelése</a>';
                }
            ?>
            <!-- Új menüpontok az adott quiz kitöltése és az eredmények megtekintése számára -->
            <?php if (isset($_SESSION['room_id'])): ?>
                <li><a href="quiz.php">Quiz Kitöltése</a></li>
                <li><a href="quiz_toplist.php">Szoba Eredményei</a></li>
            <?php endif; ?>
            <li><a href="../controllers/logout.php">Kijelentkezés</a></li>

        <?php else: ?>
            <li><a href="register.php">Regisztráció</a></li>
            <li><a href="login.php">Bejelentkezés</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>
