<?php

function connect_to_database() {
    $USERNAME = 'C##IKP7YW';                  
    $PASSWORD = 'C##IKP7YW'; 
    $dbstr1 ="
    (DESCRIPTION =
    (ADDRESS_LIST =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    )
    (CONNECT_DATA =
    (SID = orania2)
    )
    )";

    $conn = oci_connect($USERNAME,$PASSWORD,$dbstr1,'AL32UTF8');
    if(!$conn){
        $m = oci_error();
        echo $m['message'], "\n";
        echo "Your Connection Has an error";
        return null;
    } else {
        return $conn;
    }
}
function close_database_connection($conn) {
    if ($conn) {
        oci_close($conn);
    }
}
function is_admin($conn, $user_id) {
        if (!$user_id) {
            return;
        }
        // Ellenőrizzük, hogy az adott felhasználó admin-e
        $query = oci_parse($conn, "SELECT admin_e FROM felhasznalo WHERE id = :user_id");
        oci_bind_by_name($query, ":user_id", $user_id);
        oci_execute($query);
        $row = oci_fetch_assoc($query);
        return ($row && $row['ADMIN_E'] == 1);
}

function is_creator_or_admin($conn, $user_id, $room_id) {
    if (!$user_id || !$room_id) {
        return false;
    }
    $query_check_creator_or_admin = oci_parse($conn, "
        SELECT COUNT(*) AS count
        FROM szoba
        WHERE id = :room_id
        AND (felhasznalo_id = :user_id OR felhasznalo_id IN (SELECT id FROM felhasznalo WHERE admin_e = 1))
    ");

    oci_bind_by_name($query_check_creator_or_admin, ":room_id", $room_id);
    oci_bind_by_name($query_check_creator_or_admin, ":user_id", $user_id);
    
    oci_execute($query_check_creator_or_admin);
    
    $row = oci_fetch_assoc($query_check_creator_or_admin);
    
    return $row['COUNT'] > 0;
}

?>

