<?php
session_start();
include('../../includes/functions.php');
include('../../config/db_connection.php');
redirect_if_authenticatedLogin();

// Ellenőrizze, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ellenőrizze, hogy meg van-e adva a szobaazonosító a munkamenetben
if (!isset($_SESSION['room_id'])) {
    header("Location: room.php");
    exit();
}

// Ellenőrizze, hogy a room_id a munkamenetben megegyezik-e az URL-ben lévővel
if ($_SESSION['room_id'] !== $_GET['room_id']) {
    header("Location: room.php");
    exit();
}

// Az adatok elérhetők a munkamenetből
$user_id = $_SESSION['user_id'];
$room_id = $_SESSION['room_id'];

// Az adatbázis kapcsolat megnyitása
$conn = connect_to_database();

// Ha az űrlap elküldésekor POST kérés érkezik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Quiz létrehozása vagy módosítása
    if (isset($_POST['create_quiz'])) {
        // Ellenőrizd a beérkezett adatokat és hozd létre a quizt az adatbázisban
        // Példakód:
        $question = $_POST['question'];
        $theme_id = $_POST['theme_id'];
        $query = oci_parse($conn, "INSERT INTO kerdes (kerdes, tema_id) VALUES (:kerdes, :tema_id)");
        oci_bind_by_name($query, ":kerdes", $question);
        oci_bind_by_name($query, ":tema_id", $theme_id);
        oci_execute($query);
        // Kezelj más teendőket, amelyek a quiz létrehozásához szükségesek
    } elseif (isset($_POST['edit_quiz'])) {
        // Ellenőrizd a beérkezett adatokat és módosítsd a quizt az adatbázisban
        // Példakód:
        $question_id = $_POST['question_id'];
        $updated_question = $_POST['updated_question'];
        $updated_theme_id = $_POST['updated_theme_id'];
        $query = oci_parse($conn, "UPDATE kerdes SET kerdes = :kerdes, tema_id = :tema_id WHERE id = :question_id");
        oci_bind_by_name($query, ":kerdes", $updated_question);
        oci_bind_by_name($query, ":tema_id", $updated_theme_id);
        oci_bind_by_name($query, ":question_id", $question_id);
        oci_execute($query);
        // Kezelj más teendőket, amelyek a quiz módosításához szükségesek
    }
}

// Temák lekérése az adatbázisból
$themes = get_themes($conn);

// Kérdések lekérése az adatbázisból a megadott szobához
$questions = get_room_questions($conn, $room_id);

// Adatbázis kapcsolat lezárása
close_database_connection($conn);

// Tema lekérdezés az adatbázisból
function get_themes($conn) {
    $themes = [];
    $query = oci_parse($conn, "SELECT * FROM tema");
    oci_execute($query);
    while ($row = oci_fetch_assoc($query)) {
        $themes[] = $row;
    }
    return $themes;
}

// Kérdések lekérése az adatbázisból a megadott szobához
function get_room_questions($conn, $room_id) {
    $questions = [];
    $query = oci_parse($conn, "SELECT * FROM kerdes INNER JOIN szoba_kerdesei ON kerdes.id = szoba_kerdesei.kerdes_id WHERE szoba_kerdesei.szoba_id = :room_id");
    oci_bind_by_name($query, ":room_id", $room_id);
    oci_execute($query);
    while ($row = oci_fetch_assoc($query)) {
        $questions[] = $row;
    }
    return $questions;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Quiz</title>
</head>
<body>

    <h2>Room Quiz</h2>

    <!-- Quiz létrehozása form -->
    <h3>Quiz létrehozása:</h3>
    <form action="room_quiz.php?room_id=<?php echo $room_id; ?>" method="post">
        <label for="question">Kérdés:</label><br>
        <input type="text" id="question" name="question" required><br>

        <label for="theme_id">Téma:</label><br>
        <select id="theme_id" name="theme_id" required>
            <?php foreach ($themes as $theme): ?>
                <option value="<?php echo $theme['ID']; ?>"><?php echo $theme['NEV']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit" name="create_quiz">Quiz létrehozása</button>
    </form>

    <!-- Quiz lista vagy módosítása form -->
    <h3>Quiz lista vagy módosítása:</h3>
    <ul>
        <?php foreach ($questions as $question): ?>
            <li>
                <?php echo $question['KERDES']; ?>
                <form action="room_quiz.php?room_id=<?php echo $room_id; ?>" method="post" style="display: inline;">
                    <input type="hidden" name="question_id" value="<?php echo $question['ID']; ?>">
                    <input type="text" name="updated_question" value="<?php echo $question['KERDES']; ?>">
                    <select name="updated_theme_id">
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?php echo $theme['ID']; ?>" <?php if ($theme['ID'] === $question['TEMA_ID']) echo "selected"; ?>><?php echo $theme['NEV']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="edit_quiz">Szerkesztés</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

</body>
</html>
