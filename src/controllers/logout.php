<?php
session_start();

// Session változók törlése
session_unset();

// Session törlése
session_destroy();

// Átirányítás a bejelentkező oldalra
header("Location: ../views/login.php");
exit();
?>
