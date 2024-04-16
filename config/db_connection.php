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

    $conn = oci_connect($USERNAME,$PASSWORD,$dbstr1);

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
?>