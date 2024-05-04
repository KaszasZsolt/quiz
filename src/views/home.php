<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../../public_html/css/home.css">
</head>
<body >
    <?php include('../controllers/home_controller.php'); ?>
    <?php include('./header.php'); ?>
    <div class="container">
        <h2>Üdvözöllek, <?php echo $user['NEV']; ?>!</h2>
        <p>Ez az home oldal. Ide csak bejelentkezés után juthatsz.</p>
    </div>
    <h2>Statisztikák</h2>

    <div class="toplist-container">
        <?php
        // Adatbázis kapcsolat létrehozása
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        // Lekérdezés az adatbázisból az első 5 szoba és a hozzájuk tartozó kérdések száma alapján
        $sql = "
            SELECT sk.szoba_id, s.nev AS szoba_neve, COUNT(k.id) AS kerdesek_szama
            FROM szoba_kerdesei sk
            JOIN kerdes k ON sk.kerdes_id = k.id
            JOIN szoba s ON sk.szoba_id = s.id
            GROUP BY sk.szoba_id, s.nev
            ORDER BY COUNT(k.id) DESC
        ";

        $query = oci_parse($conn, $sql);
        oci_execute($query);

        // Az eredmény kiírása
        echo "<h2>Szobák a legtöbb kérdéssel:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Szoba név</th>
                    <th>Kérdések száma</th>
                </tr>";

        $counter = 0;
        while ($row = oci_fetch_assoc($query)) {
            if ($counter >= 5) break; // Csak az első 5 szobát írjuk ki
            echo "<tr>
                    <td>" . $row['SZOBA_NEVE'] . "</td>
                    <td>" . $row['KERDESEK_SZAMA'] . "</td>
                  </tr>";
            $counter++;
        }

        echo "</table>";

        // Adatbázis kapcsolat lezárása
        oci_close($conn);
        ?>
    </div>
    <div class="toplist-container">
        <?php
        // Adatbázis kapcsolat létrehozása
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        // Lekérdezés az adatbázisból az első 5 témára és előfordulásaik számára
        $sql = "
            SELECT t.nev AS tema_neve, COUNT(*) AS eloformulasok_szama
            FROM tema t
            JOIN kerdes k ON t.id = k.tema_id
            GROUP BY t.nev
            ORDER BY COUNT(*) DESC
            FETCH FIRST 5 ROWS ONLY
        ";

        $query = oci_parse($conn, $sql);
        oci_execute($query);

        // Az eredmény kiírása
        echo "<h2>Öt leggyakoribb téma:</h2>";
        echo "<table border='1'>
        <tr>
            <th>Téma neve</th>
            <th>Előfordulások száma</th>
        </tr>";

        while ($row = oci_fetch_assoc($query)) {
            echo "<tr>
                    <td>" . $row['TEMA_NEVE'] . "</td>
                    <td>" . $row['ELOFORMULASOK_SZAMA'] . "</td>
                </tr>";
        }
        echo "</table>";

        // Adatbázis kapcsolat lezárása
        oci_close($conn);
        ?>


        <?php
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        // Lekérdezés az adatbázisból az első 5 szoba és azok aktív felhasználóinak száma alapján
        $sql = "
            SELECT s.nev AS szoba_neve, COUNT(DISTINCT e.felhasznalo_id) AS aktiv_felhasznalok_szama
            FROM eredmeny e
            JOIN szoba s ON e.szoba_id = s.id
            GROUP BY s.nev
            ORDER BY COUNT(DISTINCT e.felhasznalo_id) DESC
        ";

        $query = oci_parse($conn, $sql);
        oci_execute($query);

        // Az eredmény kiírása
        echo "<h2>Legtöbb aktív felhasználóval rendelkező szobák:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Szoba név</th>
                    <th>Aktív felhasználók száma</th>
                </tr>";

        $counter = 0;
        while ($row = oci_fetch_assoc($query)) {
            if ($counter >= 5) break; // Csak az első 5 szobát írjuk ki
            echo "<tr>
                    <td>" . $row['SZOBA_NEVE'] . "</td>
                    <td>" . $row['AKTIV_FELHASZNALOK_SZAMA'] . "</td>
                </tr>";
            $counter++;
        }

        echo "</table>";

        // Adatbázis kapcsolat lezárása
        oci_close($conn);
        ?>



        <?php
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }  
        // Lekérdezés az adatbázisból az első 5 szoba és azok kitöltéseinek száma alapján
        $sql = "
            SELECT s.nev AS szoba_neve, COUNT(e.id) AS kitoltesek_szama
            FROM szoba s
            JOIN eredmeny e ON s.id = e.szoba_id
            GROUP BY s.nev
            ORDER BY COUNT(e.id) DESC
        ";

        $query = oci_parse($conn, $sql);
        oci_execute($query);

        // Az eredmény kiírása
        echo "<h2>Öt legtöbb kitöltéssel rendelkező szoba:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Szoba név</th>
                    <th>Kitöltések száma</th>
                </tr>";

        $counter = 0;
        while ($row = oci_fetch_assoc($query)) {
            if ($counter >= 5) break; // Csak az első 5 szobát írjuk ki
            echo "<tr>
                    <td>" . $row['SZOBA_NEVE'] . "</td>
                    <td>" . $row['KITOLTESEK_SZAMA'] . "</td>
                </tr>";
            $counter++;
        }

        echo "</table>";

        // Adatbázis kapcsolat lezárása
        oci_close($conn);
        ?>
    </div>
</body>
</html>
