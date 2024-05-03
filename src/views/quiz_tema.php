<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz témák kezelése</title>
</head>
<body>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include('../../includes/functions.php');
    include('../../config/db_connection.php');

    redirect_if_authenticatedLogin();
    $conn = connect_to_database();
    if ($conn && !is_admin($conn, $_SESSION['user_id'])) {
        header("Location: home.php");
        exit();
    }

    include('./header.php');
    $user_id = $_SESSION['user_id'];

    $query = oci_parse($conn, "SELECT admin_e FROM felhasznalo WHERE id = :user_id");
    oci_bind_by_name($query, ":user_id", $user_id);
    oci_execute($query);
    $user = oci_fetch_assoc($query);

    if ($user['ADMIN_E'] == 1) {
        echo '<h3>Quiz témák kezelése</h3>';
        
        // Űrlap megjelenítése új tétel hozzáadásához
        echo '<div class="container">';
        echo '<h4>Új téma hozzáadása</h4>';
        echo '<form action="" method="post">';
        echo '<input type="text" name="uj_tema_nev" placeholder="Új téma neve">';
        echo '<button type="submit" name="hozzaadas">Hozzáadás</button>';
        echo '</form>';
        
        // Új téma hozzáadása
        if (isset($_POST["hozzaadas"])) {
            $uj_tema_nev = $_POST["uj_tema_nev"];
            $query_insert = oci_parse($conn, "INSERT INTO tema (nev) VALUES (:uj_tema_nev)");
            oci_bind_by_name($query_insert, ":uj_tema_nev", $uj_tema_nev);
            oci_execute($query_insert);
            // Frissítsük az oldalt, hogy a hozzáadott téma megjelenjen
            afterPostMethod("Új téma hozzáadása sikeres.");
        }
        
        // Quiz témák lekérdezése az adatbázisból
        $query_temak = oci_parse($conn, "SELECT id, nev FROM tema");
        oci_execute($query_temak);
        echo '</div>';
        
        echo '<h4>Meglévő témák szerkesztése és törlése</h4>';
        echo '<ul>';
        while ($tema = oci_fetch_assoc($query_temak)) {
            echo '<li>' . $tema['NEV'] . ' - ';
            echo '<form action="" method="post">';
            echo '<input type="hidden" name="szerkesztes_tema_id" value="' . $tema['ID'] . '">';
            echo '<input type="text" name="szerkesztes_tema_nev" value="' . $tema['NEV'] . '">';
            echo '<button type="submit" name="szerkesztes">Módosítás</button>';
            echo '<button type="submit" name="torles" onclick="return confirm(\'Biztosan törli?\')">Törlés</button>';
            echo '</form>';
            echo '</li>';
        }
        echo '</ul>';
        
        // Szerkesztés vagy törlés
        if (isset($_POST["szerkesztes"])) {
            $szerkesztes_tema_id = $_POST["szerkesztes_tema_id"];
            $szerkesztes_tema_nev = $_POST["szerkesztes_tema_nev"];
            $query_update = oci_parse($conn, "UPDATE tema SET nev = :szerkesztes_tema_nev WHERE id = :szerkesztes_tema_id");
            oci_bind_by_name($query_update, ":szerkesztes_tema_nev", $szerkesztes_tema_nev);
            oci_bind_by_name($query_update, ":szerkesztes_tema_id", $szerkesztes_tema_id);
            oci_execute($query_update);
            // Frissítsük az oldalt, hogy a módosítások megjelenjenek
            
            afterPostMethod("Sikeres szerkesztes");
        } elseif (isset($_POST["torles"])) {
            $torles_tema_id = $_POST["szerkesztes_tema_id"];
            $query_delete = oci_parse($conn, "DELETE FROM tema WHERE id = :torles_tema_id");
            oci_bind_by_name($query_delete, ":torles_tema_id", $torles_tema_id);
            oci_execute($query_delete);
            // Frissítsük az oldalt, hogy a törlés megjelenjen
            afterPostMethod("Sikeres törlés");

        }
        
    } else {
        echo 'Nincs jogosultsága az admin részhez.';
    }

    close_database_connection($conn);
    ?>
</body>
</html>
