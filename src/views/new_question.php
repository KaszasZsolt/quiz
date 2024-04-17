<?php
session_start();
include('../../config/db_connection.php');

$conn = connect_to_database();

// Ellenőrizze, hogy a felhasználó POST kérésben küldte-e el az új kérdést
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_question'])) {
    $question_text = $_POST['question_text'];
    $topic_id = $_POST['topic_id'];
    $user_id = $_SESSION['user_id']; // A felhasználó ID-jének beolvasása a munkamenetből

    // Kérdés hozzáadása az adatbázishoz
    $query = oci_parse($conn, "INSERT INTO kerdes (kerdes, tema_id, user_id, global_question) VALUES (:kerdes, :tema_id, :user_id, :global_question)");
    oci_bind_by_name($query, ":kerdes", $question_text);
    oci_bind_by_name($query, ":tema_id", $topic_id);
    oci_bind_by_name($query, ":user_id", $user_id);
    oci_bind_by_name($query, ":global_question", isset($_POST['global_question']) ? 1 : 0); // Megadja, hogy globális kérdés-e
    oci_execute($query);
    $question_id = oci_last_insert_id($conn); // Az utolsó beszúrt kérdés azonosítójának lekérése

    // Válaszok hozzáadása az adatbázishoz
    if (isset($_POST['num_of_answers'])) {
        $num_of_answers = (int)$_POST['num_of_answers'];
        for ($i = 1; $i <= $num_of_answers; $i++) {
            if (!empty($_POST["answer_$i"])) {
                $answer_text = $_POST["answer_$i"];
                $is_correct = (isset($_POST["correct_answer_$i"])) ? 1 : 0;

                $query = oci_parse($conn, "INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (:kerdes_id, :valasz, :helyes_e)");
                oci_bind_by_name($query, ":kerdes_id", $question_id);
                oci_bind_by_name($query, ":valasz", $answer_text);
                oci_bind_by_name($query, ":helyes_e", $is_correct);
                oci_execute($query);
            }
        }
    }

    echo "Sikeresen hozzáadva az adatbázishoz!";
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

    echo "Sikeresen törölve az adatbázisból!";
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
<h2>Új kérdés hozzáadása</h2>

<!-- Űrlap új kérdés hozzáadásához -->
<form action="new_question.php" method="post">
    <label for="question_text">Kérdés szövege:</label><br>
    <textarea id="question_text" name="question_text" rows="4" cols="50" required></textarea><br><br>

    <label for="topic_id">Téma:</label><br>
    <select id="topic_id" name="topic_id" required>
        <?php while ($theme = oci_fetch_assoc($query_themes)): ?>
            <option value="<?php echo $theme['ID']; ?>"><?php echo $theme['NEV']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <!-- Válaszok -->
    <!-- Legördülő lista a válaszok számának kiválasztásához -->
    <label for="num_of_answers">Válaszok száma:</label><br>
    <select id="num_of_answers" name="num_of_answers" required>
        <?php for ($i = 2; $i <= 10; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select><br><br>

    <!-- Válaszok -->
    <?php if (isset($_POST['num_of_answers'])): ?>
        <?php $num_of_answers = (int)$_POST['num_of_answers']; ?>
        <?php for ($i = 1; $i <= $num_of_answers; $i++): ?>
            <label for="answer_<?php echo $i; ?>">Válasz <?php echo $i; ?>:</label><br>
            <input type="text" id="answer_<?php echo $i; ?>" name="answer_<?php echo $i; ?>" required><br>
        <?php endfor; ?>
    <?php endif; ?>

    <!-- Helyes válaszok kiválasztása -->
    <label for="correct_answers">Helyes válaszok:</label><br>
    <?php if (isset($_POST['num_of_answers'])): ?>
        <?php $num_of_answers = (int)$_POST['num_of_answers']; ?>
        <?php for ($i = 1; $i <= $num_of_answers; $i++): ?>
            <input type="checkbox" id="correct_answer_<?php echo $i; ?>" name="correct_answer_<?php echo $i; ?>">
            <label for="correct_answer_<?php echo $i; ?>">Válasz <?php echo $i; ?></label><br>
        <?php endfor; ?>
    <?php endif; ?>

    <!-- Globális kérdés jelölése -->
    <label for="global_question">Globális kérdés:</label>
    <input type="checkbox" id="global_question" name="global_question"><br><br>

    <!-- Küldés gomb -->
    <button type="submit" name="submit_question">Kérdés hozzáadása</button>
</form>

<hr>

<h2>Kérdések és válaszok</h2>

<!-- Kérdések listázása -->
<?php foreach ($questions as $question_id => $question): ?>
    <h3><?php echo $question['question_text']; ?></h3>
    <ul>
        <!-- Válaszok listázása -->
        <?php foreach ($question['answers'] as $answer_id => $answer): ?>
            <li>
                <?php echo $answer['answer_text']; ?>
                <?php echo ($answer['is_correct'] == 1) ? "(Helyes válasz)" : ""; ?>
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
