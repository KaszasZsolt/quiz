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

// Ha a felhasználó törlése megtörténik
if (isset($_POST['delete_user'])) {
    $userToDelete = $_POST['delete_user'];

    // SQL lekérdezés a felhasználó törlésére
    $deleteUserQuery = oci_parse($conn, "DELETE FROM felhasznalo WHERE id = :userid");
    oci_bind_by_name($deleteUserQuery, ":userid", $userToDelete);
    oci_execute($deleteUserQuery);
    header("Refresh:0");
    // Frissítjük az oldalt, hogy a törlés megjelenjen
    exit();
}
if (isset($_POST['edit_user'])) {
    $userToEdit = $_POST['edit_user'];
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    
    echo '<div class="container">';
    echo $user_name;
    echo '<form method="post">';
    echo '<input type="hidden" name="user_id" value="' . $userToEdit . '">';
    echo '<input type="text" name="new_username" value="' . $user_name . '">';
    echo $user_email;
    echo '<input type="text" name="new_useremail" value="' . $user_email . '">';
    echo '<button type="submit" name="modify_user">Mentés</button>';
    echo '</form>';
    echo '</div>';
}
if (isset($_POST['modify_user'])) {
    $userId = $_POST['user_id'];
    $newUsername = $_POST['new_username'];
    $newUseremail = $_POST['new_useremail'];


    $updateUserQuery = oci_parse($conn, "UPDATE felhasznalo SET nev = :new_username, email = :new_useremail WHERE id = :user_id");
    oci_bind_by_name($updateUserQuery, ":new_username", $newUsername);
    oci_bind_by_name($updateUserQuery, ":new_useremail", $newUseremail);
    oci_bind_by_name($updateUserQuery, ":user_id", $userId);
    oci_execute($updateUserQuery);

    // Frissítsük az oldalt, hogy a módosítás megjelenjen
    header("Refresh:0");
    exit();
}


$sql = "
        SELECT 
            felhasznalo.id As felhasznalo_id,
            felhasznalo.email As felhasznalo_email,
            felhasznalo.nev AS felhasznalo_neve, 
            TO_CHAR(felhasznalo.utolso_aktivitas_datum, 'YYYY-MM-DD HH24:MI:SS') AS utolso_aktivitas_datum,
            MAX(COALESCE(eredmeny.pontszam, 0)) AS utolso_pontszam
        FROM felhasznalo
        LEFT JOIN eredmeny ON felhasznalo.id = eredmeny.felhasznalo_id
        GROUP BY felhasznalo.id, felhasznalo.email, felhasznalo.nev, felhasznalo.utolso_aktivitas_datum
";

$query = oci_parse($conn, $sql);

// Bindelés
oci_define_by_name($query, 'FELHASZNALO_ID', $felhasznalo_id);
oci_define_by_name($query, 'FELHASZNALO_EMAIL', $felhasznalo_email);
oci_define_by_name($query, 'FELHASZNALO_NEVE', $felhasznalo_neve);
oci_define_by_name($query, 'UTOLSO_AKTIVITAS_DATUM', $utolso_aktivitas_datum);
oci_define_by_name($query, 'UTOLSO_PONTSZAM', $utolso_pontszam);

oci_execute($query);

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

<h2>Felhasználók</h2>
<table class="result-table">
    <tr>
        <th>Felhasználó neve</th>
        <th>Felhasználó email</th>
        <th>Utolsó aktivitás dátuma</th>
        <th>Utolsó pontszám</th>
        <th>Műveletek</th>
    </tr>
    <?php while (oci_fetch($query)): ?>
    <tr>
        <td><?php echo $felhasznalo_neve; ?></td>
        <td><?php echo $felhasznalo_email; ?></td>
        <td><?php echo $utolso_aktivitas_datum; ?></td>
        <td><?php echo $utolso_pontszam; ?></td>
        <td>
            <form method="post" style="display: inline;">
                <input type="hidden" name="edit_user" value="<?php echo $felhasznalo_id; ?>">
                <input type="hidden" name="user_name" value="<?php echo $felhasznalo_neve; ?>">
                <input type="hidden" name="user_email" value="<?php echo $felhasznalo_email; ?>">

                <button type="submit">Módosítás</button>
            </form>
            <form method="post" onsubmit="return confirm('Biztosan törli?');" style="display: inline;">
                <input  type="hidden" name="delete_user" value="<?php echo $felhasznalo_id; ?>">
                <button type="submit" name="torles">Törlés</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
