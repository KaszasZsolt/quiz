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

// Ha az űrlap elküldésekor POST kérés érkezik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kérdések és válaszok mentése
    if (isset($_POST['correct_answers'])) {
        $correct_answers = $_POST['correct_answers'];
        $pontszam = evaluateQuiz($conn, $room_id, $user_id, $correct_answers);
        // Átirányítás a toplistára
        
        header("Location: quiz_toplist.php");
        exit(); // Fontos: Az exit() hívás megakadályozza, hogy a további HTML kód futtatása után az átirányítás végrehajtódjon
    }
}

// Adott szobához tartozó összes kérdés kiértékelése
function evaluateQuiz($conn, $room_id, $user_id, $correct_answers) {
    // Pontszám kezdeti értéke
    $pontszam = 0;

    // Minden kérdés kiértékelése
    foreach ($correct_answers as $question_id => $selected_answers) {
        // Helyes válaszok lekérése az adott kérdéshez
        $query_correct_answers = oci_parse($conn, "
            SELECT id
            FROM valasz
            WHERE kerdes_id = :question_id AND helyes_e = 1
        ");
        oci_bind_by_name($query_correct_answers, ":question_id", $question_id);
        oci_execute($query_correct_answers);

        // Az adott kérdéshez tartozó helyes válaszok összegyűjtése
        $correct_answer_ids = [];
        while ($row = oci_fetch_assoc($query_correct_answers)) {
            $correct_answer_ids[] = $row['ID'];
        }

        // Helyes válaszok és felhasználó által kiválasztott válaszok összehasonlítása
        if (count($correct_answer_ids) === count($selected_answers) && count(array_diff($correct_answer_ids, $selected_answers)) === 0) {
            // Minden helyes válasz meg lett találva
            $pontszam++;
        }
    }

    // Eredmény mentése az adatbázisba
    $query_insert_result = oci_parse($conn, "
        INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam)
        VALUES (:room_id, :user_id, :pontszam)
    ");
    oci_bind_by_name($query_insert_result, ":room_id", $room_id);
    oci_bind_by_name($query_insert_result, ":user_id", $user_id);
    oci_bind_by_name($query_insert_result, ":pontszam", $pontszam);
    oci_execute($query_insert_result);

    return $pontszam;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
</head>
<body>
    <?php include('./header.php'); ?>

    <h2>Quiz</h2>

    <?php
    // Végrehajtjuk a lekérdezést
    $query_room_questions = oci_parse($conn, "
        SELECT k.tema_id, k.id AS kerdes_id, k.kerdes, v.id AS valasz_id, v.valasz, v.helyes_e
        FROM szoba_kerdesei sk
        JOIN kerdes k ON sk.kerdes_id = k.id
        JOIN valasz v ON k.id = v.kerdes_id
        WHERE sk.szoba_id = :room_id
        GROUP BY k.tema_id, k.id, k.kerdes, v.id, v.valasz, v.helyes_e
        ORDER BY k.tema_id
    ");
    oci_bind_by_name($query_room_questions, ":room_id", $room_id);
    oci_execute($query_room_questions);

    // HTML táblázat létrehozása az eredmények megjelenítéséhez
    echo "<form action='' method='post'>";
    echo "<table border='1'>";
    echo "<tr><th>Kérdés</th><th>Válasz</th><th>Helyes</th></tr>";

    while ($row = oci_fetch_array($query_room_questions, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr>";
        echo "<td>".$row['KERDES']."</td>";
        echo "<td>".$row['VALASZ']."</td>";
        // Helyes válasz jelölőnégyzetek hozzáadása minden sorhoz
        echo "<td>";
        echo "<input type='checkbox' name='correct_answers[".$row['KERDES_ID']."][]' value='".$row['VALASZ_ID']."'>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<button type='submit'>Kérdések és válaszok mentése</button>";
    echo "</form>";
    ?>
</body>
</html>
