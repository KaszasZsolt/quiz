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

$is_creator_or_admin = is_creator_or_admin($conn, $user_id, $room_id);

if (!$is_creator_or_admin) {
    header("Location: quiz.php");
    exit();
}

// Ha az űrlap elküldésekor POST kérés érkezik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kérdés és válaszok beállítása
    if (isset($_POST['save_question'])) {
        // Kérdés mentése
        $question = $_POST['question'];
        $theme_id = $_POST['theme_id'];
        $is_global = isset($_POST['is_global']) ? 1 : 0;

        // Kérdés beszúrása a 'kerdes' táblába
        $query_insert_question = oci_parse($conn, "INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id, globalis_kerdes) VALUES (:question, :theme_id, :user_id, :is_global)");
        oci_bind_by_name($query_insert_question, ":question", $question);
        oci_bind_by_name($query_insert_question, ":theme_id", $theme_id);
        oci_bind_by_name($query_insert_question, ":user_id", $user_id);
        oci_bind_by_name($query_insert_question, ":is_global", $is_global);
        oci_execute($query_insert_question);

        // Legutóbb beszúrt kérdés ID-jének lekérése
        $last_question_id = oci_parse($conn, "SELECT MAX(id) AS last_id FROM kerdes");
        oci_execute($last_question_id);
        $question_id_row = oci_fetch_assoc($last_question_id);
        $last_question_id = $question_id_row['LAST_ID'];

        // A kérdés beszúrása a 'SZOBA_KERDESEI' táblába
        $query_insert_room_question = oci_parse($conn, "INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (:room_id, :question_id)");
        oci_bind_by_name($query_insert_room_question, ":room_id", $room_id);
        oci_bind_by_name($query_insert_room_question, ":question_id", $last_question_id);
        oci_execute($query_insert_room_question);

        // Válaszok mentése és a helyes válasz beállítása
        for ($i = 1; $i <= $_POST['answer_count']; $i++) {
            $answer = $_POST['answer_' . $i];
            $is_correct = isset($_POST['correct_answer_' . $i]) ? 1 : 0;

            // Válasz beszúrása a 'valasz' táblába
            $query_insert_answer = oci_parse($conn, "INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (:question_id, :answer, :is_correct)");
            oci_bind_by_name($query_insert_answer, ":question_id", $last_question_id);
            oci_bind_by_name($query_insert_answer, ":answer", $answer);
            oci_bind_by_name($query_insert_answer, ":is_correct", $is_correct);
            oci_execute($query_insert_answer);
        }
        afterPostMethod("Sikeresen hozzáadva");
    }
}


// Témák lekérése az adatbázisból
$query_themes = oci_parse($conn, "SELECT * FROM tema");
oci_execute($query_themes);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kérdések és válaszok beállítása</title>
</head>
<body>
    <?php include('./header.php'); ?>
    <div class="container">
    <h2>Kérdések és válaszok beállítása</h2>

    <form action="" method="post">
        <label for="question">Kérdés:</label><br>
        <input type="text" id="question" name="question" required><br>

        <label for="theme_id">Téma:</label><br>
        <select id="theme_id" name="theme_id" required>
            <?php while ($theme = oci_fetch_assoc($query_themes)): ?>
                <option value="<?php echo $theme['ID']; ?>"><?php echo $theme['NEV']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="is_global">Globális kérdés?</label>
        <input type="checkbox" id="is_global" name="is_global"><br><br>

        <script>
            function updateAnswers(minValue, maxValue) {
                var answerCount = document.getElementById("answer_count").value;
                if (answerCount < minValue) {
                    answerCount = minValue;
                } else if (answerCount > maxValue) {
                    answerCount = maxValue;
                }
                document.getElementById("answer_count").value = answerCount;

                var answerContainer = document.getElementById("answer_container");
                answerContainer.innerHTML = "";

                for (var i = 1; i <= answerCount; i++) {
                    var label = document.createElement("label");
                    label.setAttribute("for", "answer_" + i);
                    label.textContent = "Válasz " + i + ":";

                    var input = document.createElement("input");
                    input.setAttribute("type", "text");
                    input.setAttribute("id", "answer_" + i);
                    input.setAttribute("name", "answer_" + i);
                    input.setAttribute("required", "required");

                    var checkbox = document.createElement("input");
                    checkbox.setAttribute("type", "checkbox");
                    checkbox.setAttribute("id", "correct_answer_" + i);
                    checkbox.setAttribute("name", "correct_answer_" + i);
                    
                    var checkboxLabel = document.createElement("label");
                    checkboxLabel.setAttribute("for", "correct_answer_" + i);
                    checkboxLabel.textContent = "Helyes válasz";

                    answerContainer.appendChild(label);
                    answerContainer.appendChild(document.createElement("br"));
                    answerContainer.appendChild(input);
                    answerContainer.appendChild(checkbox);
                    answerContainer.appendChild(checkboxLabel);
                    answerContainer.appendChild(document.createElement("br"));
                    answerContainer.appendChild(document.createElement("br"));
                }
            }
            </script>


        <label for="answer_count">Válaszok száma:</label>
        <input type="number" id="answer_count" name="answer_count" min="2" max="10" value="4" onchange="updateAnswers(2,10)" required><br><br>

        <div id="answer_container">
            <?php
            $answer_count = isset($_POST['answer_count']) ? $_POST['answer_count'] : 4;
            for ($i = 1; $i <= $answer_count; $i++):
            ?>
                <div class="answer">
                    <label for="answer_<?php echo $i; ?>">Válasz <?php echo $i; ?>:</label><br>
                    <input type="text" id="answer_<?php echo $i; ?>" name="answer_<?php echo $i; ?>" required>
                    <div class="checkbox-label-inline">
                        <input type="checkbox" id="correct_answer_<?php echo $i; ?>" name="correct_answer_<?php echo $i; ?>">
                        <label for="correct_answer_<?php echo $i; ?>">Helyes válasz</label>
                    </div>
                </div>
            <?php endfor; ?>
        </div>


        <button type="submit" name="save_question">Kérdés és válaszok mentése</button>
    </form>
    </div>

    <h3>Szobához tartozó kérdések</h3>
    <?php
        // Végrehajtjuk a lekérdezést
        $query_room_questions = oci_parse($conn, "
            SELECT k.*, v.*
            FROM szoba_kerdesei sk
            JOIN kerdes k ON sk.kerdes_id = k.id
            JOIN valasz v ON k.id = v.kerdes_id
            WHERE sk.szoba_id = :room_id
        ");
        oci_bind_by_name($query_room_questions, ":room_id", $room_id);
        oci_execute($query_room_questions);

        // HTML táblázat létrehozása az eredmények megjelenítéséhez
        echo "<table border='1'>";
        echo "<tr><th>Kérdés</th><th>Válasz</th></tr>";

        while ($row = oci_fetch_array($query_room_questions, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>";
            echo "<td>".$row['KERDES']."</td>";
            // Ellenőrizzük, hogy a válasz helyes-e
            if ($row['HELYES_E'] == 1) {
                // Ha helyes, zöld színnel jelenítjük meg
                echo "<td style='color: green;'>".$row['VALASZ']."</td>";
            } else {
                // Ha helytelen, piros színnel jelenítjük meg
                echo "<td style='color: red;'>".$row['VALASZ']."</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    ?>
</body>
</html>
