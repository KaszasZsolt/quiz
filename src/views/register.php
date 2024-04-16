<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
</head>
<body>
    <h2>Regisztráció</h2>

    <?php include('../controllers/register_controller.php'); ?>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="register.php" method="post">
    <label for="username">Felhasználónév:</label><br>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Jelszó:</label><br>
    <input type="password" id="password" name="password" required><br>

    <label for="confirm_password">Jelszó megerősítése:</label><br>
    <input type="password" id="confirm_password" name="confirm_password" required><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br>

    <label for="full_name">Teljes név:</label><br>
    <input type="text" id="full_name" name="full_name" required><br><br>

    <button type="submit">Regisztráció</button>
</form>
<p>Már van fiókod? <a href="login.php">Jelentkezz be itt!</a></p>
</body>
</html>
