<?php
session_start();
include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedLogin();

// Szükséges adatok lekérése a munkamenetből
$user_id = $_SESSION['user_id'];
$room_id = $_SESSION['room_id'];

// Adatbázis kapcsolat létrehozása
$conn = connect_to_database();

// Ha az űrlap elküldésekor POST kérés érkezik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kérdés és válaszok beállítása
    if (isset($_POST['save_question'])) {
        // Kérdés mentése
        $question = $_POST['question'];
        $query_insert_question = oci_parse($conn, "INSERT INTO kerdes (kerdes, tema_id) VALUES (:question, :theme_id)");
        oci_bind_by_name($query_insert_question, ":question", $question);
        oci_bind_by_name($query_insert_question, ":theme_id", $_POST['theme_id']);
        oci_execute($query_insert_question);

        // Legutóbb beszúrt kérdés ID-jének lekérése
        $last_question_id = oci_parse($conn, "SELECT MAX(id) AS last_id FROM kerdes");
        oci_execute($last_question_id);
        $question_id_row = oci_fetch_assoc($last_question_id);
        $last_question_id = $question_id_row['LAST_ID'];

        // Válaszok mentése és a helyes válasz beállítása
        for ($i = 1; $i <= 4; $i++) {
            $answer = $_POST['answer_' . $i];
            $is_correct = isset($_POST['correct_answer_' . $i]) ? 1 : 0;
            $query_insert_answer = oci_parse($conn, "INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (:question_id, :answer, :is_correct)");
            oci_bind_by_name($query_insert_answer, ":question_id", $last_question_id);
            oci_bind_by_name($query_insert_answer, ":answer", $answer);
            oci_bind_by_name($query_insert_answer, ":is_correct", $is_correct);
            oci_execute($query_insert_answer);
        }
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

        <?php for ($i = 1; $i <= 4; $i++): ?>
            <label for="answer_<?php echo $i; ?>">Válasz <?php echo $i; ?>:</label><br>
            <input type="text" id="answer_<?php echo $i; ?>" name="answer_<?php echo $i; ?>" required>
            <input type="checkbox" id="correct_answer_<?php echo $i; ?>" name="correct_answer_<?php echo $i; ?>">
            <label for="correct_answer_<?php echo $i; ?>">Helyes válasz</label><br><br>
        <?php endfor; ?>

        <button type="submit" name="save_question">Kérdés és válaszok mentése</button>
    </form>
</body>
</html>
