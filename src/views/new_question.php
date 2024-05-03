<?php
session_start();
include('../../config/db_connection.php');
include('../../includes/functions.php');

$conn = connect_to_database();

redirect_if_authenticatedLogin();
if ($conn && !is_admin($conn, $_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

include('./header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kérdés és válaszok beállítása
    if (isset($_POST['save_question'])) {
        // Kérdés mentése
        $question = $_POST['question'];
        $theme_id = $_POST['theme_id'];
        $is_global = 1;

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


// Ellenőrizze, hogy a felhasználó POST kérésben küldte-e el a kérdés és válasz törlését
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $question_id = $_POST['question_id'];

    // Kérdés törlése az adatbázisból
    $query = oci_parse($conn, "DELETE FROM kerdes WHERE id = :id");
    oci_bind_by_name($query, ":id", $question_id);
    oci_execute($query);

    // A kérdéshez tartozó válaszok törlése az adatbázisból
    $query = oci_parse($conn, "DELETE FROM valasz WHERE kerdes_id = :kerdes_id");
    oci_bind_by_name($query, ":kerdes_id", $question_id);
    oci_execute($query);

    afterPostMethod("Sikeresen törölve az adatbázisból!");
}

// Témák lekérése az adatbázisból
$query_themes = oci_parse($conn, "SELECT * FROM tema");
oci_execute($query_themes);

// Kérdések és válaszok lekérése az adatbázisból
$query = oci_parse($conn, "SELECT k.id AS question_id, k.kerdes AS question_text, v.id AS answer_id, v.valasz AS answer_text, v.helyes_e AS is_correct, k.globalis_kerdes AS is_global FROM kerdes k LEFT JOIN valasz v ON k.id = v.kerdes_id");
oci_execute($query);
$questions = [];
while ($row = oci_fetch_assoc($query)) {
    $questions[$row['QUESTION_ID']]['question_text'] = $row['QUESTION_TEXT'];
    $questions[$row['QUESTION_ID']]['global_question'] = $row['IS_GLOBAL'];
    $questions[$row['QUESTION_ID']]['answers'][$row['ANSWER_ID']] = ['answer_text' => $row['ANSWER_TEXT'], 'is_correct' => $row['IS_CORRECT']];
}

// Adatbázis kapcsolat lezárása
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új kérdés hozzáadása</title>
</head>
<body>

<div class="container">
<h2>Új kérdés hozzáadása</h2>
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
                        <label for="correct_answer_<?php echo $i; ?>">Helyes válasz</label><br><br>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <button type="submit" name="save_question">Kérdés és válaszok mentése</button>
    </form>
 </div>


<hr>

<h2>Kérdések és válaszok</h2>

<!-- Kérdések listázása -->
<?php foreach ($questions as $question_id => $question): ?>
    <h3><?php echo $question['question_text']; ?></h3>
    <ul>
        <!-- Válaszok listázása -->
        <?php foreach ($question['answers'] as $answer_id => $answer): ?>
    <li>
        <div <?php echo ($answer['is_correct'] == 1) ? "class='green-text'" : ""; ?>>
            <?php echo $answer['answer_text']; ?>
            <?php echo ($answer['is_correct'] == 1) ? "(Helyes válasz)" : ""; ?>
        </div>
    </li>
<?php endforeach; ?>
    </ul>
    <!-- Kérdés törlésének űrlapja -->
    <form action="new_question.php" method="post">
        <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
        <button type="submit" name="delete_question">Kérdés törlése</button>
    </form>
    <hr>
<?php endforeach; ?>
</body>
</html>
