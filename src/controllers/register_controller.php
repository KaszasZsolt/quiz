<?php
session_start();
include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedHome();

$conn = connect_to_database();
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];

    // Ellenőrzések
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($full_name)) {
        $errors[] = "Minden mező kitöltése kötelező!";
    } else {
        if ($password !== $confirm_password) {
            $errors[] = "A jelszavak nem egyeznek!";
        }
        
        // Ellenőrizd, hogy a felhasználónév már létezik-e
        $checkUserQuery = oci_parse($conn, "SELECT * FROM felhasznalo WHERE nev = :username");
        oci_bind_by_name($checkUserQuery, ":username", $username);
        oci_execute($checkUserQuery);

        if (oci_fetch_array($checkUserQuery)) {
            $errors[] = "A felhasználónév már foglalt!";
        }

        if (empty($errors)) {
            // Regisztráció
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = oci_parse($conn, "INSERT INTO felhasznalo (nev, jelszo, email, admin_e) VALUES (:username, :password, :email, 0)");
            oci_bind_by_name($insertQuery, ":username", $username);
            oci_bind_by_name($insertQuery, ":password", $hashed_password);
            oci_bind_by_name($insertQuery, ":email", $email);
            
            if (oci_execute($insertQuery)) {
                echo "Sikeres regisztráció!";
                header("Location: ../views/login.php?register=success");
                exit();
            } else {
                $errors[] = "Hiba a regisztráció közben!";
            }
            // Átirányítás a sikeres regisztrációs oldalra
            exit();
        }
    }
}
close_database_connection($conn);
?>
