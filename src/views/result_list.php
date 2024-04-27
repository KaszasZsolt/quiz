<?php
session_start();
include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedLogin();
$conn = connect_to_database();

// Definiáljuk a $result változót, mielőtt átadjuk az eljárásnak
$result = oci_new_cursor($conn);

$stmt = oci_parse($conn, "BEGIN get_all_room_results(:result); END;");
oci_bind_by_name($stmt, ':result', $result, -1, OCI_B_CURSOR);
oci_execute($stmt);

// Fetch result set into PHP array
echo "<table border='1'>";
echo "<tr><th>Szoba ID</th><th>Felhasználó ID</th><th>Pontszám</th></tr>";

while (($row = oci_fetch_array($result, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
    echo "<tr>";
    echo "<td>".$row['SZOBA_ID']."</td>";
    echo "<td>".$row['FELHASZNALO_ID']."</td>";
    echo "<td>".$row['PONTSZAM']."</td>";
    echo "</tr>";
}

echo "</table>";

// Free the statement and close the connection
oci_free_statement($stmt);
oci_close($conn);
?>
