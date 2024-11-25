<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és admin joggal rendelkezik
if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] != 1) {
    header('Location: bejelentkezes.php');
    exit;
}

// Adatbázis kapcsolódás
$host = 'localhost';
$dbname = 'flavorwave';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
}

// Többi kód változatlan...


// Felhasználói jogosultság ellenőrzése
$stmt = $pdo->prepare("SELECT jog_szint FROM felhasznalo WHERE id = :id");
$stmt->execute(['id' => $_SESSION['felhasznalo_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['jog_szint'] != 1) {
    header('Location: kezdolap.php');
    exit;
}

$message = '';

// Hozzáadás
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operation'])) {
    $operation = $_POST['operation'];

    if ($operation === 'add') {
        $nev = $_POST['nev'];
        $egyseg_ar = $_POST['egyseg_ar'];
        $leiras = $_POST['leiras'];
        $kategoria_id = $_POST['kategoria_id'];
        $kepek_url = $_FILES['kepek_url']['name'];

        // Kép feltöltése
        if ($kepek_url) {
            $target_dir = '../kepek/';
            $target_file = $target_dir . basename($kepek_url);
            if (!move_uploaded_file($_FILES['kepek_url']['tmp_name'], $target_file)) {
                $message = "Hiba a kép feltöltése során!";
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO etel (nev, egyseg_ar, leiras, kategoria_id, kep_url) 
                    VALUES (:nev, :egyseg_ar, :leiras, :kategoria_id, :kep_url)"
                );
                $stmt->execute([
                    ':nev' => $nev,
                    ':egyseg_ar' => $egyseg_ar,
                    ':leiras' => $leiras,
                    ':kategoria_id' => $kategoria_id,
                    ':kep_url' => $kepek_url
                ]);
                $message = "Sikeres hozzáadás!";
            }
        }
    }

    // Szerkesztés
    if ($operation === 'edit') {
        $id = $_POST['edit_etel'];
        $nev = $_POST['edit_nev'];
        $egyseg_ar = $_POST['edit_egyseg_ar'];
        $leiras = $_POST['edit_leiras'];
        $kategoria_id = $_POST['edit_kategoria_id'];
        $kepek_url = $_FILES['edit_kepek_url']['name'];

        if ($kepek_url) {
            $target_dir = 'uploads/';
            $target_file = $target_dir . basename($kepek_url);
            if (move_uploaded_file($_FILES['edit_kepek_url']['tmp_name'], $target_file)) {
                $kepek_url_sql = ", kep_url = :kep_url";
            } else {
                $message = "Hiba a kép feltöltése során!";
                $kepek_url_sql = "";
            }
        } else {
            $kepek_url_sql = "";
        }

        $stmt = $pdo->prepare(
            "UPDATE etel 
            SET nev = :nev, egyseg_ar = :egyseg_ar, leiras = :leiras, kategoria_id = :kategoria_id $kepek_url_sql 
            WHERE id = :id"
        );

        $params = [
            ':nev' => $nev,
            ':egyseg_ar' => $egyseg_ar,
            ':leiras' => $leiras,
            ':kategoria_id' => $kategoria_id,
            ':id' => $id
        ];
        if ($kepek_url_sql) $params[':kep_url'] = $kepek_url;

        if ($stmt->execute($params)) {
            $message = "Sikeres szerkesztés!";
        } else {
            $message = "Hiba a szerkesztés során!";
        }
    }

    // Törlés
    if ($operation === 'delete') {
        $id = $_POST['delete_etel'];
        $stmt = $pdo->prepare("DELETE FROM etel WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            $message = "Sikeres törlés!";
        } else {
            $message = "Hiba a törlés során!";
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
    <link rel="stylesheet" href="../css/admin_felulet.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <h1>Admin Felület</h1>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <select name="operation" id="operation" required>
            <option value="">Válasszon műveletet</option>
            <option value="add">Hozzáadás</option>
            <option value="edit">Szerkesztés</option>
            <option value="delete">Törlés</option>
        </select>

        <div id="add-form">
            <h3>Hozzáadás</h3>
            <input type="text" name="nev" placeholder="Név" required>
            <input type="number" name="egyseg_ar" placeholder="Egységár" required>
            <textarea name="leiras" placeholder="Leírás" required></textarea>
            <select name="kategoria_id" required>
                <option value="1">Italok</option>
                <option value="2">Ételek</option>
            </select>
            <input type="file" name="kepek_url" accept="image/*" required>
            <button type="submit">Hozzáadás</button>
        </div>
    </form>
</body>
</html>
