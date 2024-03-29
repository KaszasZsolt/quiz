<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];

    if ($password != $confirm_password) {
        die("A megadott jelszavak nem egyeznek!");
    }

    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Sikeresen regisztráltál!";
    } else {
        echo "Hiba: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Regisztráció</title>
</head>
<body>

<h2>Regisztráció</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Felhasználónév: <input type="text" name="username"><br><br>
    Jelszó: <input type="password" name="password"><br><br>
    Jelszó megerősítése: <input type="password" name="confirm_password"><br><br>
    E-mail: <input type="text" name="email"><br><br>
    <input type="submit" name="submit" value="Regisztráció">
</form>

</body>
</html>
