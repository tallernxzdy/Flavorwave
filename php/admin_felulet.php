<?php
// Adatbázis kapcsolódás
$host = 'localhost';
$dbname = 'flavorwave';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Hiba: " . $e->getMessage();
}

$message = '';  // Hiba/siker üzenet változó

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit']) && $_POST['operation'] == 'add') {
        // Hozzáadás
        $nev = $_POST['nev'];
        $egyseg_ar = $_POST['egyseg_ar'];
        $leiras = $_POST['leiras'];
        $kategoria_id = $_POST['kategoria_id'];
        $kepek_url = $_FILES['kepek_url']['name'];

        // Kép feltöltése
        if ($kepek_url) {
            $target_dir = 'uploads/';
            $target_file = $target_dir . basename($kepek_url);
            // Ellenőrizzük, hogy a fájl feltöltése sikeres-e
            if (!move_uploaded_file($_FILES['kepek_url']['tmp_name'], $target_file)) {
                $message = "Hiba a kép feltöltése során!";
            }
        }

        // Kategória ellenőrzés
        $sql_check = "SELECT COUNT(*) FROM kategoria WHERE id = :kategoria_id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':kategoria_id', $kategoria_id);
        $stmt_check->execute();
        $category_exists = $stmt_check->fetchColumn();

        if ($category_exists > 0) {
            // SQL lekérdezés
            $sql = "INSERT INTO etel (nev, egyseg_ar, leiras, kategoria_id, kepek_url) 
                    VALUES (:nev, :egyseg_ar, :leiras, :kategoria_id, :kepek_url)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nev', $nev);
            $stmt->bindParam(':egyseg_ar', $egyseg_ar);
            $stmt->bindParam(':leiras', $leiras);
            $stmt->bindParam(':kategoria_id', $kategoria_id);
            $stmt->bindParam(':kepek_url', $kepek_url);

            if ($stmt->execute()) {
                $message = "Sikeres hozzáadás!";
            } else {
                $message = "Hiba történt a hozzáadás során.";
            }
        } else {
            $message = "Hiba: A kiválasztott kategória nem létezik!";
        }
    }

    if (isset($_POST['edit_submit']) && $_POST['operation'] == 'edit') {
        // Szerkesztés
        $id = $_POST['edit_etel'];
        $nev = $_POST['edit_nev'];
        $egyseg_ar = $_POST['edit_egyseg_ar'];
        $leiras = $_POST['edit_leiras'];
        $kategoria_id = $_POST['edit_kategoria_id'];
        $kepek_url = $_FILES['edit_kepek_url']['name'];

        // Ha új kép van, feltöltjük
        if ($kepek_url) {
            $target_dir = 'uploads/';
            $target_file = $target_dir . basename($kepek_url);
            if (!move_uploaded_file($_FILES['edit_kepek_url']['tmp_name'], $target_file)) {
                $message = "Hiba a kép feltöltése során!";
            }
        } else {
            // Ha nem történt új kép feltöltése, az eredeti képet használjuk
            $sql = "SELECT kepek_url FROM etel WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $kepek_url = $row['kepek_url'];
        }

        // Kategória ellenőrzés
        $sql_check = "SELECT COUNT(*) FROM kategoria WHERE id = :kategoria_id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':kategoria_id', $kategoria_id);
        $stmt_check->execute();
        $category_exists = $stmt_check->fetchColumn();

        if ($category_exists > 0) {
            // SQL lekérdezés
            $sql = "UPDATE etel SET nev = :nev, egyseg_ar = :egyseg_ar, leiras = :leiras, kategoria_id = :kategoria_id, kepek_url = :kepek_url WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nev', $nev);
            $stmt->bindParam(':egyseg_ar', $egyseg_ar);
            $stmt->bindParam(':leiras', $leiras);
            $stmt->bindParam(':kategoria_id', $kategoria_id);
            $stmt->bindParam(':kepek_url', $kepek_url);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $message = "Sikeres szerkesztés!";
            } else {
                $message = "Hiba történt a szerkesztés során.";
            }
        } else {
            $message = "Hiba: A kiválasztott kategória nem létezik!";
        }
    }

    if (isset($_POST['delete_submit']) && $_POST['operation'] == 'delete') {
        // Törlés
        $id = $_POST['delete_etel'];

        // SQL lekérdezés
        $sql = "DELETE FROM etel WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $message = "Sikeres törlés!";
        } else {
            $message = "Hiba történt a törlés során.";
        }
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
    <link rel="stylesheet" href="../css/admin_felulet.css">
</head>

<body>
<nav class="navbar">
        <div class="navbar-container">
            <a href="kezdolap.php" class="logo">🌊 Flavorwave</a>
            <ul class="navbar_ul">
                <li><a class="navbar_link special_link kozepso" href="kezdolap.php">Főoldal</a></li>
                <li><a class="navbar_link special_link kozepso" href="menu.php">Menü</a></li>
            </ul>
            <div class="right_links">
                <?php if (isset($_SESSION["username"])): ?>
                    <a class="navbar_link logout" href="kijelentkezes.php">Kijelentkezés</a>
                <?php else: ?>
                    <a class="navbar_link login" href="bejelentkezes.php">Bejelentkezés</a>
                    <a class="navbar_link register" href="regisztracio.php">Regisztráció</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="admin-container">
        <h1>Admin Felület</h1>

        <!-- Üzenet megjelenítése -->
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Műveletek űrlap -->
        <form action="admin_felulet.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="operation">Válasszon műveletet:</label>
                <select id="operation" name="operation" required>
                    <option value="">Válasszon műveletet</option>
                    <option value="add">Hozzáadás</option>
                    <option value="edit">Szerkesztés</option>
                    <option value="delete">Törlés</option>
                </select>
            </div>

            <!-- Hozzáadás űrlap -->
            <div id="add-form" class="operation-form" style="display:none;">
                <h3>Új étel hozzáadása</h3>
                <div class="form-group">
                    <label for="nev">Név:</label>
                    <input type="text" id="nev" name="nev" required>
                </div>
                <div class="form-group">
                    <label for="egyseg_ar">Egységár:</label>
                    <input type="number" id="egyseg_ar" name="egyseg_ar" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="leiras">Leírás:</label>
                    <textarea id="leiras" name="leiras" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="kategoria_id">Kategória:</label>
                    <select id="kategoria_id" name="kategoria_id" required>
                        <option value="1">Italok</option>
                        <option value="2">Ételek</option>
                        <option value="3">Desszertek</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kepek_url">Kép feltöltése:</label>
                    <input type="file" id="kepek_url" name="kepek_url" accept="image/*" required>
                </div>
                <button type="submit" name="submit" class="btn">Hozzáadás</button>
            </div>

            <!-- Szerkesztés űrlap -->
            <div id="edit-form" class="operation-form" style="display: none;">
                <h3>Étel szerkesztése</h3>
                <div class="form-group">
                    <label for="edit_etel">Étel választás:</label>
                    <select id="edit_etel" name="edit_etel" required>
                        <?php
                        $sql = "SELECT id, nev FROM etel";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nev']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_nev">Név:</label>
                    <input type="text" id="edit_nev" name="edit_nev" required>
                </div>
                <div class="form-group">
                    <label for="edit_egyseg_ar">Egységár:</label>
                    <input type="number" id="edit_egyseg_ar" name="edit_egyseg_ar" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="edit_leiras">Leírás:</label>
                    <textarea id="edit_leiras" name="edit_leiras" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_kategoria_id">Kategória:</label>
                    <select id="edit_kategoria_id" name="edit_kategoria_id" required>
                        <option value="1">Italok</option>
                        <option value="2">Ételek</option>
                        <option value="3">Desszertek</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_kepek_url">Új kép feltöltése (opcionális):</label>
                    <input type="file" id="edit_kepek_url" name="edit_kepek_url" accept="image/*">
                </div>
                <button type="submit" name="edit_submit" class="btn">Szerkesztés</button>
            </div>

            <!-- Törlés űrlap -->
            <div id="delete-form" class="operation-form" style="display:none;">
                <h3>Étel törlése</h3>
                <div class="form-group">
                    <label for="delete_etel">Étel választás:</label>
                    <select id="delete_etel" name="delete_etel" required>
                        <?php
                        $sql = "SELECT id, nev FROM etel";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nev']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="delete_submit" class="btn">Törlés</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('operation').addEventListener('change', function () {
            var operation = this.value;
            document.getElementById('add-form').style.display = (operation === 'add') ? 'block' : 'none';
            document.getElementById('edit-form').style.display = (operation === 'edit') ? 'block' : 'none';
            document.getElementById('delete-form').style.display = (operation === 'delete') ? 'block' : 'none';
        });
    </script>
</body>

</html>
