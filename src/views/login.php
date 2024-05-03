<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
</head>
<body>
<?php include('./header.php'); ?>
<div class="container">

    <h2>Bejelentkezés</h2>
    <?php include('../controllers/login_controller.php'); ?>

    <?php if (!empty($register_message)): ?>
        <p><?php echo $register_message; ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="login.php" method="post">
        <label for="username">Felhasználónév:</label><br>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Jelszó:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Bejelentkezés</button>
    </form>
    <p>Még nincs fiókod? <a href="register.php">Regisztrálj itt!</a></p>
</div>
</body>
</html>
