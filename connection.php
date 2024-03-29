<?php
// Oracle adatbázis kapcsolódási információk
$hostname = 'orania2.inf.u-szeged.hu'; // Az Oracle adatbázis host neve vagy IP címe
$port = '1521'; // Az Oracle adatbázis portja
$sid = 'orania2'; // Az Oracle adatbázis SID-je vagy neve
$username = 'C##W4U3NE'; // Az Oracle adatbázis felhasználóneve
$password = 'C##W4U3NE'; // Az Oracle adatbázis jelszava

// Oracle adatbázishoz csatlakozás
$conn = oci_connect($username, $password, "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$hostname)(PORT=$port))(CONNECT_DATA=(SID=$sid)))");

// Csatlakozás ellenőrzése
if (!$conn) {
    $error = oci_error();
    die("Csatlakozási hiba: " . $error['message']);
} else {
    echo "Sikeresen csatlakozva az Oracle adatbázishoz!";
    
    // Itt folytathatod a lekérdezéseket vagy más műveleteket
}

// Oracle adatbázisról való lecsatlakozás
oci_close($conn);
?>
