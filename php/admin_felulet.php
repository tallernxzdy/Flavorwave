<?php
session_start();
include 'adatbazisra_csatlakozas.php';

// Jogosultság ellenőrzése
if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] != 1) {
    header('Location: bejelentkezes.php');
    exit;
}

$message = "";

// Kategóriák lekérdezése

$kategoriak = adatokLekerdezese("SELECT id, kategoria_nev FROM kategoria");
if (!is_array($kategoriak)) {
    $message = "<div class='alert alert-warning'> Hiba a kategóriák lekérdezése során: $kategoriak </div>";
}

// Ételek lekérdezése
$etelek = adatokLekerdezese("SELECT id, nev FROM etel");
if (!is_array($etelek)) {
    $message = "<div class='alert alert-warning'> Hiba az ételek lekérdezése során: $etelek </div>";
}

// Művelet feldolgozása
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operation = $_POST['operation'];

    // Hozzáadás
    if ($operation === 'add') {
        $nev = $_POST['nev'];
        $egyseg_ar = $_POST['egyseg_ar'];
        $leiras = $_POST['leiras'];
        $kategoria_id = $_POST['kategoria_id'];

        // Kép feltöltése
        $kep_url = "";
        if (isset($_FILES['kepek_url']['name']) && $_FILES['kepek_url']['name'] !== "") {
            $target_dir = "../kepek/";
            $uniqueName = basename($_FILES['kepek_url']['name']);
            $target_file = $target_dir . $uniqueName;
            if (is_file($target_file)){
                $message = "<div class='alert alert-warning'>Ez a fájl már létezik</div>";
            }
            else{
                if (move_uploaded_file($_FILES['kepek_url']['tmp_name'], $target_file)) {
                    $kep_url = $uniqueName; // Csak a fájl nevét mentjük
                } else {
                    $message = "<div class='alert alert-warning'>Hiba a kép feltöltése során!</div>";
                }
            }
        }
        // Ellenőrzés, hogy a név létezik-e már
        $lekerdezes = "SELECT COUNT(*) as count FROM etel WHERE nev = ?";
        $result = adatokLekerdezese($lekerdezes, ['s', $nev]);
        $alreadyExitsts = false;
        if(is_array($result) && $result[0]['count'] > 0){
            $alreadyExitsts = true;
            $message = "<div class='alert alert-warning'>Ez az étel már létezik</div>";
        }
        
        else{
            // Adatbázisba mentés
            if ($kep_url || !empty($_FILES['kepek_url']['name'])) {
                $muvelet = "INSERT INTO etel (nev, egyseg_ar, leiras, kategoria_id, kep_url) VALUES (?, ?, ?, ?, ?)";
                $parameterek = ['sssis', $nev, $egyseg_ar, $leiras, $kategoria_id, $kep_url];
                $result = adatokValtoztatasa($muvelet, $parameterek);
                $message = $result;
            }
            $message = "<div class='alert alert-info'>Sikeres feltöltés!</div>";
        }
    }

    // Szerkesztés
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $operation === 'edit') { 
        $id = $_POST['edit_etel'];
        $nev = $_POST['edit_nev'];
        $egyseg_ar = $_POST['edit_egyseg_ar'];
        $leiras = $_POST['edit_leiras'];
        $kategoria_id = $_POST['edit_kategoria_id'];
    
        // Kép frissítése, ha van új kép
        $kep_url = ""; // Alapértelmezett változó inicializálása
        $kep_url_sql = ""; // SQL részlet a kép frissítéséhez
        if (isset($_FILES['edit_kepek_url']['name']) && $_FILES['edit_kepek_url']['name'] !== "") {
            $target_dir = "../kepek/";
            $uniqueName = uniqid() . '-' . basename($_FILES['edit_kepek_url']['name']);
            $target_file = $target_dir . $uniqueName;
    
            if (move_uploaded_file($_FILES['edit_kepek_url']['tmp_name'], $target_file)) {
                $kep_url_sql = ", kep_url = ?";
                $kep_url = $uniqueName; // Csak a fájl nevét mentjük
            } else {
                $message = "<div class='alert alert-info'>Hiba a kép feltöltése során! </div>";
            }
        } else {
            // Ha nincs új kép, lekérdezzük az aktuális URL-t az adatbázisból
            $etel = adatokLekerdezese("SELECT kep_url FROM etel WHERE id = $id");
            if (is_array($etel) && count($etel) > 0) {
                $kep_url = $etel[0]['kep_url'];
            }
        }
    
        // Adatbázis frissítés
        
        $muvelet = "UPDATE etel SET nev = ?, egyseg_ar = ?, leiras = ?, kategoria_id = ? $kep_url_sql WHERE id = ?";
        $parameterek = ['sisi', $nev, $egyseg_ar, $leiras, $kategoria_id];
        
        if ($kep_url_sql != "") {
            $parameterek[0] .= 's'; // Típus stringhez hozzáadunk egy 's'-t
            array_push($parameterek, $kep_url);
        }
        $parameterek[0] .= "i";
        $parameterek[] = $id; // Az ID hozzáadása a végén
        $result = adatokValtoztatasa($muvelet, $parameterek);
        $message = $result;
    }

    // Törlés
    if ($operation === 'delete') {
        $id = $_POST['delete_etel'];

        // Kép törlése, ha létezik
        $etel = adatokLekerdezese("SELECT kep_url FROM etel WHERE id = $id");
        if (is_array($etel) && count($etel) > 0 && file_exists("../kepek/" . $etel[0]['kep_url'])) {
            unlink("../kepek/" . $etel[0]['kep_url']);
        }

        // Adatbázisból törlés
        $result = adatokTorlese("id = $id");
        $message = $result;
    }
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Felület</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/admin_felulet.css">
    <script>
        if ( window.history.replaceState){
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>
<body>

<nav>
<nav>
        <!-- Bal oldalon a logó -->
        <a href="kezdolap.php" class="logo">
            <img src="../kepek/logo.png" alt="Flavorwave Logo">
            <h1>FlavorWave</h1>
        </a>

        <!-- Középen a kategóriák (és Admin felület, ha jogosult) -->
        <div class="navbar-center">
            <a href="kategoria.php">Kategóriák</a>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <a href="admin_felulet.php">Admin felület</a>
            <?php endif; ?>
        </div>

        <!-- Jobb oldalon a gombok -->
        <div class="navbar-buttons">
            <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            <a href="kosar.php" class="cart-btn">Kosár</a>
        </div>

        <!-- Hamburger menü ikon -->
        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- Hamburger menü tartalma -->
    <div class="menubar" id="menubar">
        <ul>
            <li><a href="kategoria.php">Kategóriák</a></li>
            <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
                <li><a href="admin_felulet.php">Admin felület</a></li>
            <?php endif; ?>
            <li><a href="kosar.php">Kosár</a></li>
            <?php if (isset($_SESSION["username"])): ?>
                <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
            <?php else: ?>
                <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
            <?php endif; ?>
        </ul>
    </div>


    <div class="container">
        <h1>Admin Felület</h1>

        <?php if ($message): ?>
            <?php echo $message; ?>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <select name="operation" id="operation" class="form-select mb-3" required>
                    <option value="">Válasszon műveletet</option>
                    <option value="add">Hozzáadás</option>
                    <option value="edit">Szerkesztés</option>
                    <option value="delete">Törlés</option>
                </select>
                
            <!-- Hozzáadás -->

            <div id="add-form" class="form-section" style="display:none;">
                <h3>Hozzáadás</h3>
                <input type="text" name="nev" placeholder="Név" class="form-control mb-2" data-required>
                <input type="number" name="egyseg_ar" placeholder="Egységár" class="form-control mb-2" data-required>
                <textarea name="leiras" placeholder="Leírás" class="form-control mb-2" data-required></textarea>
                <select name="kategoria_id" class="form-select mb-2" data-required>
                    <option value="">Válassz kategóriát</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['id']) ?>">
                            <?= htmlspecialchars($kategoria['kategoria_nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="kepek_url" accept="image/*" class="form-control mb-2" data-required>
                <button type="submit" class="btn btn-primary">Hozzáadás</button>
                <p style="display:inline"><a style="float:right" href="kezdolap.php">Vissza a kezdőlapra</a></p>
            </div>

            <!-- Szerkesztés -->
            <div id="edit-form" class="form-section" style="display:none;">
                <h3>Szerkesztés</h3>
                <select name="edit_etel" class="form-select mb-2" data-required>
                    <option value="">Válassz ételt</option>
                    <?php foreach ($etelek as $etel): ?>
                        <option value="<?= htmlspecialchars($etel['id']) ?>">
                            <?= htmlspecialchars($etel['nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="edit_nev" placeholder="Név" class="form-control mb-2">
                <input type="number" name="edit_egyseg_ar" placeholder="Egységár" class="form-control mb-2">
                <textarea name="edit_leiras" placeholder="Leírás" class="form-control mb-2"></textarea>
                <select name="edit_kategoria_id" class="form-select mb-2">
                    <option value="">Válassz kategóriát</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['id']) ?>">
                            <?= htmlspecialchars($kategoria['kategoria_nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="edit_kepek_url" accept="image/*" class="form-control mb-2" data-required>
                <button type="submit" class="btn btn-primary">Szerkesztés</button>
            </div>


            <!-- Törlés -->

            <div id="delete-form" class="form-section" style="display:none;">
                <h3>Törlés</h3>
                <select name="delete_etel" class="form-select mb-2" data-required>
                    <option value="">Válassz ételt</option>
                    <?php foreach ($etelek as $etel): ?>
                        <option value="<?= htmlspecialchars($etel['id']) ?>">
                            <?= htmlspecialchars($etel['nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-danger">Törlés</button>
            </div>


        </form>
    </div>

    <script>

    document.getElementById('operation').addEventListener('change', function () {
        const sections = document.querySelectorAll('.form-section');
        
        // Elrejt minden szekciót és eltávolítja a 'required' attribútumokat
        sections.forEach(section => {
            section.style.display = 'none';
            const inputs = section.querySelectorAll('[required]');
            inputs.forEach(input => input.removeAttribute('required'));
        });

        // Megjeleníti a kiválasztott szekciót, és hozzáadja a 'required' attribútumokat
        if (this.value) {
            const activeSection = document.getElementById(this.value + '-form');
            activeSection.style.display = 'block';
            const inputs = activeSection.querySelectorAll('[data-required]');
            inputs.forEach(input => input.setAttribute('required', 'required'));
        }
    });


    </script>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>
