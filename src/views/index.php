<?php
include('../../config/db_connection.php');

$conn = connect_to_database(); 
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
    
    <div class="message-container">
        <?php
        if ($conn) {
            echo "<p class='success-message'> Sikeres kapcsolat az adatbázishoz!</p>";  
        } else {
            echo "<p class='error-message'> Nem sikerült kapcsolódni az adatbázishoz.</p>";
        }
        ?>
    </div>
</body>


</html>
