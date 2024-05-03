<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../../public_html/css/home.css">
</head>
<body >
    <?php include('../controllers/home_controller.php'); ?>
    <?php include('./header.php'); ?>
    <div class="container">
        <h2>Üdvözöllek, <?php echo $user['NEV']; ?>!</h2>
        <p>Ez az home oldal. Ide  csak bejelentkezés után juthatsz.</p>
    </div>
    



</body>
</html>
