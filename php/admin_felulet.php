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
    $message = "Hiba a kategóriák lekérdezése során: $kategoriak";
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
            $uniqueName = uniqid() . '-' . basename($_FILES['kepek_url']['name']);
            $target_file = $target_dir . $uniqueName;

            if (move_uploaded_file($_FILES['kepek_url']['tmp_name'], $target_file)) {
                $kep_url = $uniqueName; // Csak a fájl nevét mentjük
            } else {
                $message = "Hiba a kép feltöltése során!";
            }
        }

        // Adatbázisba mentés
        if ($kep_url || empty($_FILES['kepek_url']['name'])) {
            $result = adatokValtoztatasa(
                "INSERT INTO etel (nev, egyseg_ar, leiras, kategoria_id, kep_url)
                 VALUES ('$nev', '$egyseg_ar', '$leiras', '$kategoria_id', '$kep_url')"
            );
            $message = $result;
        }
    }

    // Szerkesztés
    if ($operation === 'edit') {
        $id = $_POST['edit_etel'];
        $nev = $_POST['edit_nev'];
        $egyseg_ar = $_POST['edit_egyseg_ar'];
        $leiras = $_POST['edit_leiras'];
        $kategoria_id = $_POST['edit_kategoria_id'];

        // Kép frissítése, ha van új kép
        $kep_url_sql = "";
        if (isset($_FILES['edit_kepek_url']['name']) && $_FILES['edit_kepek_url']['name'] !== "") {
            $target_dir = "../kepek/";
            $uniqueName = uniqid() . '-' . basename($_FILES['edit_kepek_url']['name']);
            $target_file = $target_dir . $uniqueName;

            if (move_uploaded_file($_FILES['edit_kepek_url']['tmp_name'], $target_file)) {
                $kep_url_sql = ", kep_url = '$uniqueName'";
            } else {
                $message = "Hiba a kép feltöltése során!";
            }
        }

        // Adatbázis frissítés
        $result = adatokValtoztatasa(
            "UPDATE etel 
             SET nev = '$nev', egyseg_ar = $egyseg_ar, leiras = '$leiras', kategoria_id = $kategoria_id $kep_url_sql 
             WHERE id = $id"
        );
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
    <link rel="stylesheet" href="../css/admin_felulet.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Admin Felület</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
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
                <input type="file" name="kepek_url" accept="image/*" class="form-control mb-2">
                <button type="submit" class="btn btn-primary">Hozzáadás</button>
                <p style="display:inline"><a style="float:right" href="kezdolap.php">Vissza a kezdőlapra</a></p>
            </div>

            <!-- Szerkesztés -->
            <div id="edit-form" class="form-section" style="display:none;">
                <h3>Szerkesztés</h3>
                <input type="number" name="edit_etel" placeholder="Étel ID" class="form-control mb-2" data-required>
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
                <input type="file" name="edit_kepek_url" accept="image/*" class="form-control mb-2">
                <button type="submit" class="btn btn-primary">Szerkesztés</button>
                <p style="display:inline"><a style="float:right" href="kezdolap.php">Vissza a kezdőlapra</a></p>

            </div>

            <!-- Törlés -->
            <div id="delete-form" class="form-section" style="display:none;">
                <h3>Törlés</h3>
                <input type="number" name="delete_etel" placeholder="Étel ID" class="form-control mb-2" data-required>
                <button type="submit" class="btn btn-danger">Törlés</button>
                <p style="display:inline"><a style="float:right" href="kezdolap.php">Vissza a kezdőlapra</a></p>
                
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
</body>
</html>
