<?php
session_start();
include('../../includes/functions.php');
include('../../config/db_connection.php');

redirect_if_authenticatedLogin();
$conn = connect_to_database();

// Ellenőrizzük, hogy a felhasználó POST kérésben küldte-e el az új szoba adatokat vagy a szerkesztendő szoba adatokat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Új szoba létrehozása
    if (isset($_POST['create_room'])) {
        if ($_POST['room_password'] !== $_POST['confirm_password']) {
            echo "A jelszó és a megerősítő jelszó nem egyezik meg!";
        } else {
            $result = create_room($conn, $_POST['room_name'], $_POST['room_password'], $_POST['confirm_password'], $_SESSION['user_id']);
            if ($result) {
                afterPostMethod("Sikeresen létrehozott egy új szobát!");
            }

        }
    }
    // Szoba szerkesztése
    elseif (isset($_POST['edit_room'])) {
        if ($_POST['new_password'] !== $_POST['confirm_new_password']) {
            echo "A jelszó és a megerősítő jelszó nem egyezik meg!";
        } else {
            $result = edit_room($conn, $_POST['room_id'], $_POST['room_name'], $_POST['new_password'], $_POST['confirm_new_password']);
            if ($result) {
                afterPostMethod("Sikeresen szerkesztette a szobát!");
            }
            

        }
    }
    // Szoba törlése
    elseif (isset($_POST['delete_room'])) {
        $result = delete_room($conn, $_POST['room_id']);
        if ($result) {
            afterPostMethod("Sikeresen törölte a szobát!");
        }
        
    }
    // Csatlakozás a szobához
    elseif (isset($_POST['join_room'])) {
        $room_id = $_POST['room_id'];
        $room_password = $_POST['room_password'];
        if (verify_room_password($conn, $room_id, $room_password)) {
            // Ha a szoba jelszója megfelelő, irányítson át a room_quiz.php-re
            header("Location: room_quiz.php?room_id=$room_id");
            exit();
        } else {
            afterPostMethod("Hibás szoba azonosító vagy jelszó!");
        }
    }
}

// Szobák lekérése a felhasználóhoz
$rooms = get_user_rooms($conn, $_SESSION['user_id']);

// Adatbázis kapcsolat lezárása

// Függvény a szoba létrehozásához
function create_room($conn, $room_name, $room_password, $confirm_password, $user_id) {
    // Ellenőrizzük, hogy a jelszó és a megerősítő jelszó megegyezik-e
    if ($room_password !== $confirm_password) {
        return false;
    }

    // Jelszó hashelése
    $hashed_password = password_hash($room_password, PASSWORD_DEFAULT);
    
    // SQL lekérdezés előkészítése az új szoba létrehozásához
    $query = oci_parse($conn, "INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES (:nev, :jelszo, :user_id)");
    oci_bind_by_name($query, ":nev", $room_name);
    oci_bind_by_name($query, ":jelszo", $hashed_password);
    oci_bind_by_name($query, ":user_id", $user_id);

    // Lekérdezés végrehajtása
    oci_execute($query);
    return true;
}

// Függvény a szoba szerkesztéséhez
function edit_room($conn, $room_id, $room_name, $new_password, $confirm_new_password) {
    // Ellenőrizzük, hogy a jelszó és a megerősítő jelszó megegyezik-e
    if ($new_password !== $confirm_new_password) {
        return false;
    }

    // Jelszó hashelése
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // SQL lekérdezés előkészítése a szoba szerkesztéséhez
    $query = oci_parse($conn, "UPDATE szoba SET nev = :nev, jelszo = :jelszo WHERE id = :id");
    oci_bind_by_name($query, ":nev", $room_name);
    oci_bind_by_name($query, ":jelszo", $hashed_password);
    oci_bind_by_name($query, ":id", $room_id);

    // Lekérdezés végrehajtása
    oci_execute($query);
    return true;
}

// Függvény a szoba törléséhez
function delete_room($conn, $room_id) {
    // SQL lekérdezés előkészítése a szoba törléséhez
    $query = oci_parse($conn, "DELETE FROM szoba WHERE id = :id");
    oci_bind_by_name($query, ":id", $room_id);

    // Lekérdezés végrehajtása
    return oci_execute($query);
}

// Függvény a felhasználóhoz tartozó szobák lekéréséhez
function get_user_rooms($conn, $user_id) {
    // SQL lekérdezés előkészítése a felhasználóhoz tartozó szobák lekéréséhez
    $query = oci_parse($conn, "SELECT * FROM szoba WHERE felhasznalo_id = :user_id");
    oci_bind_by_name($query, ":user_id", $user_id);

    // Lekérdezés végrehajtása
    oci_execute($query);

    // Lekérdezés eredményének tárolása egy tömbben
    $rooms = [];
    while ($row = oci_fetch_assoc($query)) {
        $rooms[] = $row;
    }

    // Lekérdezés eredményének visszaadása
    return $rooms;
}

// Függvény a szoba jelszavának ellenőrzéséhez
function verify_room_password($conn, $room_id, $room_password) {
    // SQL lekérdezés előkészítése a szoba jelszavának ellenőrzéséhez
    $query = oci_parse($conn, "SELECT jelszo FROM szoba WHERE id = :id");
    oci_bind_by_name($query, ":id", $room_id);

    // Lekérdezés végrehajtása
    oci_execute($query);

    // Jelszó ellenőrzése
    $row = oci_fetch_assoc($query);
    if ($row && password_verify($room_password, $row['JELSZO'])) {
        $_SESSION['room_id'] = $room_id;
        return true;
    } else {
        return false;
    }
}

close_database_connection($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szobák Kezelése</title>
</head>
<body>
<?php include('./header.php'); ?>

       <!-- Csatlakozási lehetőség -->
       <div class="container">
        <h3>Szobához való csatlakozás:</h3>
        <form action="room.php" method="post">
            <label for="room_id">Szoba azonosító:</label><br>
            <input type="text" id="room_id" name="room_id" required><br>

            <label for="room_password">Szoba jelszó:</label><br>
            <input type="password" id="room_password" name="room_password" required><br><br>

            <button type="submit" name="join_room">Csatlakozás</button>
        </form>
    </div>

    <!-- Szobák listázása -->
    <div class="room-list">
    <h3>Általad létrehozott szobák és azok kezelése:</h3>
        <ul>
            <?php foreach ($rooms as $room): ?>
                <li class="room-li">
                    <div class="room-details">
                        <p>Szoba azonosító: <?php echo $room['ID']; ?></p>
                        <p>Szoba név: <?php echo $room['NEV']; ?></p>
                    </div>
                    
                    <form action="room.php" method="post" style="display: inline;">
                        <input type="hidden" name="room_id" value="<?php echo $room['ID']; ?>">
                        <input type="text" name="room_name" value="<?php echo $room['NEV']; ?>">
                        <input type="password" name="new_password" placeholder="Új jelszó">
                        <input type="password" name="confirm_new_password" placeholder="Megerősítés">
                        <button type="submit" name="edit_room">Szerkesztés</button>
                        <button type="submit" name="delete_room">Törlés</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- Új szoba létrehozása form -->
    <div class="container">
        <h3>Új szoba létrehozása:</h3>
        <form action="room.php" method="post">
            <label for="room_name">Szoba neve:</label><br>
            <input type="text" id="room_name" name="room_name" required><br>

            <label for="room_password">Jelszó:</label><br>
            <input type="password" id="room_password" name="room_password" required><br><br>

            <label for="confirm_password">Jelszó megerősítése:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit" name="create_room">Szoba létrehozása</button>
        </form>
    </div>

 

</body>
</html>
