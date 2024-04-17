<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <?php include('../controllers/home_controller.php'); ?>
    <?php include('./header.php'); ?>

    <h2>Üdvözöllek, <?php echo $user['NEV']; ?>!</h2>
    <p>Ez az home oldal. Ide  csak bejelentkezés után juthatsz.</p>

    <a href="./room.php">Szoba kezelés</a>

    <a href="../controllers/logout.php">Kijelentkezés</a>

    <a href="./new_question.php">Kérdések kezelése</a>
    <?php
        // Ellenőrizzük, hogy az aktuális felhasználó admin-e
        $conn = connect_to_database();
        if ($conn && is_admin($conn, $_SESSION['user_id'])) {
            echo '<a href="./admin.php">Adminisztráció</a>';
        }
        close_database_connection($conn);
    ?>
</body>
</html>
