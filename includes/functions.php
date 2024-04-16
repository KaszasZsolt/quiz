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
?>
