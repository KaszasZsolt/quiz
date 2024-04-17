<?php
session_start();
include('../../config/db_connection.php');

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

    echo "Sikeresen hozzáadva az adatbázishoz!";
}

// Ellenőrizzük, hogy a felhasználó POST kérésben küldte-e el a téma törlését
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_topic'])) {
    $topic_id = $_POST['topic_id'];

    // Téma törlése az adatbázisból
    $query = oci_parse($conn, "DELETE FROM tema WHERE id = :id");
    oci_bind_by_name($query, ":id", $topic_id);
    oci_execute($query);

    echo "Sikeresen törölve az adatbázisból!";
}

// Témák lekérése az adatbázisból
$query_themes = oci_parse($conn, "SELECT * FROM tema");
oci_execute($query_themes);

// Ellenőrizzük, hogy a felhasználó POST kérésben küldte-e el az eredmény törlését
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_result'])) {
    $result_id = $_POST['result_id'];

    // Eredmény törlése az adatbázisból
    $query = oci_parse($conn, "DELETE FROM eredmeny WHERE id = :id");
    oci_bind_by_name($query, ":id", $result_id);
    oci_execute($query);

    echo "Sikeresen törölve az adatbázisból!";
}

// Ellenőrizzük, hogy a felhasználó POST kérésben küldte-e el a pontszám módosítását
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_score'])) {
    $result_id = $_POST['result_id'];
    $new_score = $_POST['new_score'];

    // Pontszám frissítése az adatbázisban
    $query = oci_parse($conn, "UPDATE eredmeny SET pontszam = :new_score WHERE id = :id");
    oci_bind_by_name($query, ":new_score", $new_score);
    oci_bind_by_name($query, ":id", $result_id);
    oci_execute($query);

    echo "Pontszám sikeresen frissítve!";
}

// Eredmények lekérése az adatbázisból
$query_results = oci_parse($conn, "SELECT * FROM eredmeny");
oci_execute($query_results);

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

<h3>Eredmények</h3>
<!-- Eredmények listázása -->
<ul>
    <?php while ($result = oci_fetch_assoc($query_results)): ?>
        <li>
            Felhasználó ID: <?php echo $result['FELHASZNALO_ID']; ?> - Szoba ID: <?php echo $result['SZOBA_ID']; ?> - Pontszám: <?php echo $result['PONTSZAM']; ?>
            <!-- Pontszám módosításának űrlapja -->
            <form action="admin.php" method="post" style="display: inline;">
                <input type="hidden" name="result_id" value="<?php echo $result['ID']; ?>">
                <input type="number" name="new_score" value="<?php echo $result['PONTSZAM']; ?>" required>
                <button type="submit" name="update_score">Módosítás</button>
            </form>
            <!-- Eredmény törlésének űrlapja -->
            <form action="admin.php" method="post" style="display: inline;">
                <input type="hidden" name="result_id" value="<?php echo $result['ID']; ?>">
                <button type="submit" name="delete_result">Törlés</button>
            </form>
        </li>
    <?php endwhile; ?>
</ul>
</body>
</html>
