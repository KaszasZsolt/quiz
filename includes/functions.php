<?php

function authenticate_user($conn, $username, $password) {
    $query = oci_parse($conn, "SELECT * FROM felhasznalo WHERE nev = :username");
    oci_bind_by_name($query, ":username", $username);
    oci_execute($query);
    $user = oci_fetch_assoc($query);
    if ($user && isset($user['JELSZO']) && password_verify($password, $user['JELSZO'])) {
        return $user;
    } else {
        return null;
    }
}

function redirect_if_authenticatedHome() {
    if (isset($_SESSION['user_id'])) {
        header("Location: home.php");
        exit();
    }
}

function redirect_if_authenticatedLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function redirect_if_authenticatedRoom() {
    if (!isset($_SESSION['room_id'])) {
        header("Location: room.php");
        exit();
    }
}

function afterPostMethod($message) {
    // Előkészítjük az üzenetet a következő oldalon való megjelenítésre
    $_SESSION['message'] = $message;
    // Átirányítunk a következő oldalra
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}
function sessionStart(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

sessionStart();
// Az üzenet megjelenítése, ha van ilyen
if (isset($_SESSION['message'])) {
    echo "<p>" . $_SESSION['message'] . "</p>";
    // Üzenet törlése a SESSION-ból
    unset($_SESSION['message']);
}



?>
