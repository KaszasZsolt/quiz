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


</body>
</html>
