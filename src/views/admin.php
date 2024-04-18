<?php
session_start();
include('../../config/db_connection.php');
include('../../includes/functions.php');

// Ellenőrizzük, hogy az aktuális felhasználó admin-e
$conn = connect_to_database();

if ($conn && !is_admin($conn, $_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ellenőrizzük, hogy a felhasználó POST kérésben küldte-e el az új témát
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_topic'])) {
    $topic_name = $_POST['topic_name'];

    // Téma hozzáadása az adatbázishoz
    $query = oci_parse($conn, "INSERT INTO tema (nev) VALUES (:nev)");
    oci_bind_by_name($query, ":nev", $topic_name);
    oci_execute($query);

    afterPostMethod("Sikeresen hozzáadva az adatbázishoz!");
}

// Ellenőrizzük, hogy a felhasználó POST kérésben küldte-e el a téma törlését
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_topic'])) {
    $topic_id = $_POST['topic_id'];

    // Téma törlése az adatbázisból
    $query = oci_parse($conn, "DELETE FROM tema WHERE id = :id");
    oci_bind_by_name($query, ":id", $topic_id);
    oci_execute($query);

    afterPostMethod("Sikeresen törölve az adatbázisból!");
}

// Témák lekérése az adatbázisból
$query_themes = oci_parse($conn, "SELECT * FROM tema");
oci_execute($query_themes);




$query_results = oci_parse($conn, "SELECT eredmeny.*, felhasznalo.nev FROM eredmeny JOIN felhasznalo ON eredmeny.felhasznalo_id = felhasznalo.id");
oci_execute($query_results);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_button'])) { // Itt is változtattam
    $result_id = $_POST['delete_result'];

    $query = oci_parse($conn, "DELETE FROM eredmeny WHERE id = :id");
    oci_bind_by_name($query, ":id", $result_id);
    oci_execute($query);

    afterPostMethod("Sikeresen törölve az adatbázisból!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_button'])) { // Itt is változtattam
    $result_id = $_POST['update_score'];
    $new_score = $_POST['new_score'];

    $query = oci_parse($conn, "UPDATE eredmeny SET pontszam = :new_score WHERE id = :id");
    oci_bind_by_name($query, ":new_score", $new_score);
    oci_bind_by_name($query, ":id", $result_id);
    oci_execute($query);

    afterPostMethod("Pontszám sikeresen frissítve!");
}
// Adatbázis kapcsolat lezárása
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminisztráció</title>
</head>
<body>
<?php include('./header.php'); ?>
<h2>Adminisztráció</h2>

<h3>Témák</h3>
<!-- Űrlap új téma hozzáadásához -->
<form action="admin.php" method="post">
    <label for="topic_name">Téma neve:</label><br>
    <input type="text" id="topic_name" name="topic_name" required><br><br>

    <!-- Küldés gomb -->
    <button type="submit" name="submit_topic">Téma hozzáadása</button>
</form>

<!-- Témák listázása -->
<ul>
    <?php while ($theme = oci_fetch_assoc($query_themes)): ?>
        <li>
            <?php echo $theme['NEV']; ?>
            <!-- Téma törlésének űrlapja -->
            <form action="admin.php" method="post" style="display: inline;">
                <input type="hidden" name="topic_id" value="<?php echo $theme['ID']; ?>">
                <button type="submit" name="delete_topic">Téma törlése</button>
            </form>
        </li>
    <?php endwhile; ?>
</ul>

<h2>Eredmények</h2>
<table border='1'>
    <tr>
        <th>Felhasználó neve</th>
        <th>Pontszám</th>
        <th>Műveletek</th>
    </tr>
    <?php while ($row = oci_fetch_assoc($query_results)): ?>
    <tr>
        <td><?php echo $row['NEV']; ?></td>
        <td><?php echo $row['PONTSZAM']; ?></td>
        <td>
        <form action="admin.php" method="post" style="display: inline;">
            <input type="hidden" name="delete_result" value="<?php echo $row['ID']; ?>">
            <button type="submit" name="delete_button">Törlés</button> <!-- Itt változtattam -->
        </form>
        <form action="admin.php" method="post" style="display: inline;">
            <input type="hidden" name="update_score" value="<?php echo $row['ID']; ?>">
            <input type="number" name="new_score" value="<?php echo $row['PONTSZAM']; ?>" required>
            <button type="submit" name="update_button">Mentés</button> <!-- Itt változtattam -->
        </form>
        </td>
    </tr>
<?php endwhile; ?>
</table>
</body>
</html>
