<?php
session_start();
include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedLogin();
redirect_if_authenticatedRoom();

// Szükséges adatok lekérése a munkamenetből
$user_id = $_SESSION['user_id'];
$room_id = $_SESSION['room_id'];

// Adatbázis kapcsolat létrehozása
$conn = connect_to_database();

$avgScore = 0;
// Eljárás hívása
$sql = "BEGIN get_average_score(:userId, :avgScore); END;";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':userId', $user_id);
oci_bind_by_name($stid, ':avgScore', $avgScore, 4000);
oci_execute($stid);

$text_with_dots = str_replace(',', '.', $avgScore);
$szam = floatval($text_with_dots);


$query_user_info = oci_parse($conn, "
    SELECT nev
    FROM felhasznalo
    WHERE id = :user_id
");

oci_bind_by_name($query_user_info, ":user_id", $user_id);
oci_execute($query_user_info);
$user_info = oci_fetch_assoc($query_user_info);

$query_room_info = oci_parse($conn, "
    SELECT s.nev AS szoba_neve
    FROM szoba s
    JOIN eredmeny e ON s.id = e.szoba_id
    WHERE s.id = :room_id
    ORDER BY e.id DESC
");

oci_bind_by_name($query_room_info, ":room_id", $room_id);
oci_execute($query_room_info);
$room_info = oci_fetch_assoc($query_room_info);


$query_toplist = oci_new_cursor($conn);

$stmt = oci_parse($conn, "BEGIN get_toplist_for_room(:room_id, :result); END;");
oci_bind_by_name($stmt, ':room_id', $room_id);
oci_bind_by_name($stmt, ':result', $query_toplist, -1, OCI_B_CURSOR);

oci_execute($stmt);
oci_execute($query_toplist);


// A játékos eredményének lekérése az adott szobában
$query_player_result = oci_parse($conn, "
    SELECT pontszam
    FROM eredmeny
    WHERE felhasznalo_id = :user_id AND szoba_id = :room_id
    ORDER BY id DESC
    FETCH FIRST 1 ROWS ONLY
");
oci_bind_by_name($query_player_result, ":user_id", $user_id);
oci_bind_by_name($query_player_result, ":room_id", $room_id);
oci_execute($query_player_result);
$player_result = oci_fetch_assoc($query_player_result);

oci_free_statement($stmt);
oci_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Toplista</title>
</head>
<body>
    <?php include('./header.php'); ?>

    <div class="toplist-container">
        <h2>Quiz Toplista</h2>

       

        <div class="toplist">
            <h3>Toplista:</h3>
            <ol>
                <?php
                // Top 10 felhasználó eredményeinek kilistázása
                $rank = 1;
                while ($row = oci_fetch_assoc($query_toplist)) {
                    echo "<li>" . $row['FELHASZNALONEV'] . " - Pontszám: " . $row['PONTSZAM'] . "</li>";
                    $rank++;
                    if ($rank > 10) {
                        break;
                    }
                }
                ?>
            </ol>
        </div>
        <div class="container">
            <h3>Felhasználói adatok:</h3>
            <p>Felhasználónév: <?php echo $user_info['NEV']; ?></p>
            <p>Szoba neve: <?php echo $room_info['SZOBA_NEVE']; ?></p>
        </div>
        <div class="container">
            <h3>Játékos legutóbbi eredménye:</h3>
            <?php
            if ($player_result) {
                echo "<p>Pontszám: " . $player_result['PONTSZAM'] . "</p>";
            } else {
                echo "<p>Még nincs eredményed ebben a szobában.</p>";
            }
            ?>
            <h3>Felhasználó átlagpontszáma:</h3>
            <?php echo "Felhasználó átlagpontszáma: " . round($szam, 2); ?>
        </div>
    </div>
</body>
</html>


