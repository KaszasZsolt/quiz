<?php
include('../../config/db_connection.php');

$conn = connect_to_database(); 

if ($conn) {
    echo "Sikeres kapcsolat az adatbázishoz!";
} else {
    echo "Nem sikerült kapcsolódni az adatbázishoz.";
}

// Az adatbáziskapcsolat bezárása
if ($conn) {
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adatbázis kapcsolat ellenőrzése</title>
</head>
<body>
<?php include('./header.php'); ?>
        <form action="login.php" method="post">
            <button type="submit">Tovább a bejelentkezéshez</button>
        </form>
</body>
</html>
