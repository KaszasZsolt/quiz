<?php
session_start();

include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedLogin();

$user_id = $_SESSION['user_id'];

$conn = connect_to_database();
$query = oci_parse($conn, "SELECT nev FROM felhasznalo WHERE id = :user_id");
oci_bind_by_name($query, ":user_id", $user_id);
oci_execute($query);
$user = oci_fetch_assoc($query);
close_database_connection($conn);
?>
