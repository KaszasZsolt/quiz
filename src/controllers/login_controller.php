<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedHome();

if (isset($_GET['register']) && $_GET['register'] === 'success') {
    $register_message = "Sikeres regisztráció! Most már bejelentkezhetsz.";
} else {
    $register_message = "";
}

$conn = connect_to_database();

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Felhasználónév és jelszó megadása kötelező!";
    } else {
        $user = authenticate_user($conn, $username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['ID'];
            header("Location: ../views/home.php");
            exit();
        } else {
            $errors[] = "Hibás felhasználónév vagy jelszó!";
        }
    }
}

close_database_connection($conn);
?>
