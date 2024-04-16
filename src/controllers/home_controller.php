<?php
session_start();

include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedLogin();

$user_id = $_SESSION['user_id'];

$conn = connect_to_database();
$query = oci_parse($conn, "SELECT nev, admin_e FROM felhasznalo WHERE id = :user_id");
oci_bind_by_name($query, ":user_id", $user_id);
oci_execute($query);
$user = oci_fetch_assoc($query);

if ($user['ADMIN_E'] == 1) {
    echo '<div style="background-color: lightblue; padding: 10px; margin-bottom: 10px;">';
    echo '<h3>Admin rész</h3>';
    echo '<a href="./quiz_tema.php">Quiz témák kezelése</a>';
    echo '</div>';
}

close_database_connection($conn);
?>
