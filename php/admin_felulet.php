<?php
session_start();
include 'adatbazisra_csatlakozas.php';

$message = "";
$operation = "";

if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] != 1) {
    header('Location: bejelentkezes.php');
    exit;
}

// API mód - GET kérések kezelése
if (isset($_GET['action']) && $_GET['action'] === 'get_etel' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Érvénytelen ID!']));
    }
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM etel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(404);
        exit(json_encode(['error' => 'Étel nem található!']));
    }
    $etel = $result->fetch_assoc();
    header('Content-Type: application/json');
    exit(json_encode($etel));
}

// Kategóriák, ételek és felhasználók lekérdezése
$kategoriak = adatokLekerdezese("SELECT id, kategoria_nev FROM kategoria") ?: [];
$etelek = adatokLekerdezese("SELECT id, nev FROM etel") ?: [];
$felhasznalok = adatokLekerdezese("SELECT id, felhasznalo_nev, jog_szint FROM felhasznalo") ?: [];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operation = $_POST['operation'];

    // Hozzáadás
    if ($operation === 'add') {
        $nev = trim($_POST['nev']);
        $egyseg_ar = isset($_POST['egyseg_ar']) ? (int)$_POST['egyseg_ar'] : null;
        $leiras = trim($_POST['leiras']);
        $kategoria_id = isset($_POST['kategoria_id']) ? (int)$_POST['kategoria_id'] : null;
        $kaloria = isset($_POST['kaloria']) ? (int)$_POST['kaloria'] : null;
        $osszetevok = trim($_POST['osszetevok']);
        $allergenek = trim($_POST['allergenek']);

        // Validáció
        if ($egyseg_ar < 0 || !is_numeric($egyseg_ar)) {
            $message = "<div class='alert alert-warning'>Az egységár nem lehet negatív vagy érvénytelen szám!</div>";
        } elseif ($kaloria < 0 || !is_numeric($kaloria)) {
            $message = "<div class='alert alert-warning'>A kalória nem lehet negatív vagy érvénytelen szám!</div>";
        } elseif (empty($nev) || empty($leiras) || empty($osszetevok) || !is_numeric($kategoria_id)) {
            $message = "<div class='alert alert-warning'>Minden mező kitöltése kötelező, és a kategória érvénytelen!</div>";
        } else {
            $kep_url = "";
            if (!empty($_FILES['kepek_url']['name'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['kepek_url']['type'], $allowedTypes)) {
                    $message = "<div class='alert alert-warning'>Csak JPG, PNG vagy GIF formátumú képek tölthetők fel!</div>";
                } else {
                    $kep_url = handleImageUpload($kategoria_id, $_FILES['kepek_url'], $nev); // Közvetlenül $nev
                    if ($kep_url === false) {
                        $message = "<div class='alert alert-warning'>Már létezik ilyen nevű kép ebben a kategóriában! Válassz másik ételnevet.</div>";
                    } elseif (!$kep_url) {
                        $message = "<div class='alert alert-warning'>Hiba a kép feltöltése során!</div>";
                    }
                }
            }

            if (empty($message)) {
                $muvelet = "INSERT INTO etel (nev, egyseg_ar, leiras, kategoria_id, kep_url, kaloria, osszetevok, allergenek) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $parameterek = ['sisissss', $nev, $egyseg_ar, $leiras, $kategoria_id, $kep_url, $kaloria, $osszetevok, $allergenek];
                $result = adatokValtoztatasa($muvelet, $parameterek);
                $message = $result === 'Sikeres művelet!' 
                    ? "<div class='alert alert-success'>Étel sikeresen hozzáadva! Oldal újratöltése...</div><script>setTimeout(() => { location.reload(); }, 4000);</script>"
                    : "<div class='alert alert-warning'>Hiba: $result</div>";
            }
        }
    }

    // Szerkesztés
    if ($operation === 'edit') {
        $id = (int)$_POST['edit_etel'];
        $nev = trim($_POST['edit_nev']);
        $egyseg_ar = isset($_POST['edit_egyseg_ar']) ? (int)$_POST['edit_egyseg_ar'] : null;
        $leiras = trim($_POST['edit_leiras']);
        $kategoria_id = isset($_POST['edit_kategoria_id']) ? (int)$_POST['edit_kategoria_id'] : null;
        $kaloria = isset($_POST['edit_kaloria']) ? (int)$_POST['edit_kaloria'] : null;
        $osszetevok = trim($_POST['edit_osszetevok']);
        $allergenek = trim($_POST['edit_allergenek']);

        // Validáció
        if ($egyseg_ar < 0 || !is_numeric($egyseg_ar)) {
            $message = "<div class='alert alert-warning'>Az egységár nem lehet negatív vagy érvénytelen szám!</div>";
        } elseif ($kaloria < 0 || !is_numeric($kaloria)) {
            $message = "<div class='alert alert-warning'>A kalória nem lehet negatív vagy érvénytelen szám!</div>";
        } elseif (empty($nev) || empty($leiras) || empty($osszetevok) || !is_numeric($kategoria_id)) {
            $message = "<div class='alert alert-warning'>Minden mező kitöltése kötelező, és a kategória érvénytelen!</div>";
        } else {
            $etel = adatokLekerdezese("SELECT nev, kep_url, kategoria_id FROM etel WHERE id = ?", ['i', $id]);
            if (empty($etel)) {
                $message = "<div class='alert alert-danger'>Étel nem található!</div>";
            } else {
                $oldNev = $etel[0]['nev'] ?? '';
                $oldKepUrl = $etel[0]['kep_url'] ?? '';
                $oldKategoriaId = $etel[0]['kategoria_id'] ?? null;
                $kep_url = $oldKepUrl;
                $kepFeltoltesSikeres = false;

                // Új kép feltöltése
                if (!empty($_FILES['edit_kepek_url']['name'])) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!in_array($_FILES['edit_kepek_url']['type'], $allowedTypes)) {
                        $message = "<div class='alert alert-warning'>Csak JPG, PNG vagy GIF formátumú képek tölthetők fel!</div>";
                    } else {
                        if ($oldKepUrl) {
                            deleteImageFiles($oldKepUrl, $oldKategoriaId); // Töröljük a régi képet
                        }
                        $kep_url = handleImageUpload($kategoria_id, $_FILES['edit_kepek_url'], $nev);
                        if (!$kep_url) {
                            $message = "<div class='alert alert-danger'>Hiba az új kép feltöltésekor, lehet, hogy a fájl már létezik!</div>";
                        } else {
                            $kepFeltoltesSikeres = true;
                        }
                    }
                }
                // Név változtatása esetén
                elseif ($oldKepUrl && $nev !== $oldNev && $oldKategoriaId == $kategoria_id) {
                    $newKepUrl = renameImageFile("$oldKategoriaId/$oldKepUrl", $nev);
                    if ($newKepUrl) {
                        $kep_url = $newKepUrl;
                    } else {
                        $message = "<div class='alert alert-danger'>Hiba a kép átnevezésekor, lehet, hogy a fájl már létezik!</div>";
                    }
                }
                // Kategóriaváltás esetén
                elseif ($oldKepUrl && $oldKategoriaId != $kategoria_id) {
                    $newKepUrl = moveImageToCategory($oldKategoriaId, $kategoria_id, $oldKepUrl);
                    if ($newKepUrl) {
                        $kep_url = $newKepUrl;
                        // Biztosítjuk, hogy a régi kategóriában ne maradjon fájl
                        $oldPath = "../kepek/$oldKategoriaId/$oldKepUrl";
                        if (file_exists($oldPath)) {
                            unlink($oldPath); // Töröljük, ha még létezik
                        }
                    } else {
                        $message = "<div class='alert alert-danger'>Hiba a kép áthelyezésekor, lehet, hogy a fájl nem létezik vagy már létezik az új kategóriában!</div>";
                    }
                }

                if (empty($message)) {
                    $muvelet = "UPDATE etel SET nev = ?, egyseg_ar = ?, leiras = ?, kategoria_id = ?, kep_url = ?, kaloria = ?, osszetevok = ?, allergenek = ? WHERE id = ?";
                    $parameterek = ['sisissssi', $nev, $egyseg_ar, $leiras, $kategoria_id, $kep_url, $kaloria, $osszetevok, $allergenek, $id];
                    $result = adatokValtoztatasa($muvelet, $parameterek);

                    if ($kepFeltoltesSikeres || $result === 'Sikeres művelet!') {
                        $message = "<div class='alert alert-success'>Sikeres szerkesztés! Oldal újratöltése...</div><script>setTimeout(() => { location.reload(); }, 4000);</script>";
                    } else {
                        $message = "<div class='alert alert-danger'>Hiba: " . ($result ?: "Nem történt változás") . "</div>";
                    }
                }
            }
        }
    }

    // Törlés
    if ($operation === 'delete') {
        $id = (int)$_POST['delete_etel'];
        $etel = adatokLekerdezese("SELECT kep_url, kategoria_id FROM etel WHERE id = ?", ['i', $id]);
        if (!empty($etel[0]['kep_url'])) {
            deleteImageFiles($etel[0]['kep_url'], $etel[0]['kategoria_id']);
        }
        $result = adatokTorlese($id);
        $message = $result === 'Sikeres törlés!' 
            ? "<div class='alert alert-success'>Étel sikeresen törölve! Oldal újratöltése...</div><script>setTimeout(() => { location.reload(); }, 4000);</script>"
            : "<div class='alert alert-danger'>Hiba: $result</div>";
    }

    // Felhasználó szerkesztése
    if ($operation === 'edit_user') {
        $userId = (int)$_POST['editUserId'];
        $jogSzint = (int)$_POST['jog_szint'];
        $checkStmt = $conn->prepare("SELECT jog_szint FROM felhasznalo WHERE id = ?");
        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $checkStmt->bind_result($currentJogSzint);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($currentJogSzint == $jogSzint) {
            $message = "<div class='alert alert-warning'>A felhasználó már rendelkezik ezzel a jogosultsági szinttel!</div>";
        } else {
            $stmt = $conn->prepare("UPDATE felhasznalo SET jog_szint = ? WHERE id = ?");
            $stmt->bind_param("ii", $jogSzint, $userId);
            $message = $stmt->execute() 
                ? "<div class='alert alert-success'>A Választott profil jogosultsága sikeresen frissítve! Oldal újratöltése...</div><script>setTimeout(() => { location.reload(); }, 4000);</script>"
                : "<div class='alert alert-danger'>Hiba történt a frissítés során!</div>";
            $stmt->close();
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
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/admin_felulet.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
</head>
<body>
    <?php include './navbar.php'; ?>

    <div class="container" id="margo_felulre">
        <h1>Admin Felület</h1>
        <h5>Adatbázis műveletek</h5>
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <form id="adminForm" method="POST" enctype="multipart/form-data">
            <select name="operation" id="operation" class="form-select mb-3" required>
                <option value="">Válasszon műveletet</option>
                <option value="add">Hozzáadás</option>
                <option value="edit">Szerkesztés</option>
                <option value="delete">Törlés</option>
            </select>

            <!-- Hozzáadás űrlap -->
            <div id="add-form" class="form-section" style="display:none;">
                <h3>Hozzáadás</h3>
                <input type="text" name="nev" placeholder="Név" class="form-control mb-2" required>
                <input type="number" name="egyseg_ar" placeholder="Egységár" class="form-control mb-2" min="0" step="1" onkeydown="blockE(event)" required>
                <textarea name="leiras" placeholder="Leírás" class="form-control mb-2" required></textarea>
                <input type="number" name="kaloria" placeholder="Kalória" class="form-control mb-2" min="0" step="1" onkeydown="blockE(event)" required>
                <textarea name="osszetevok" placeholder="Összetevők" class="form-control mb-2" required></textarea>
                <textarea name="allergenek" placeholder="Allergének" class="form-control mb-2" required></textarea><select name="kategoria_id" class="form-select mb-2" required>
                <option value="">Válassz kategóriát</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['id']) ?>">
                            <?= htmlspecialchars($kategoria['kategoria_nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="kepek_url" accept="image/*" class="form-control mb-2">
                <button type="submit" data-operation="add" class="btn btn-primary">Hozzáadás</button>
            </div>

            <!-- Szerkesztés űrlap -->
            <div id="edit-form" class="form-section" style="display:none;">
                <h3>Szerkesztés</h3>
                <select name="edit_etel" class="form-select mb-2" id="editEtelSelect" required>
                    <option value="">Válassz ételt</option>
                    <?php foreach ($etelek as $etel): ?>
                        <option value="<?= htmlspecialchars($etel['id']) ?>">
                            <?= htmlspecialchars($etel['nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="edit_nev" placeholder="Név" class="form-control mb-2" required>
                <input type="number" name="edit_egyseg_ar" placeholder="Egységár" class="form-control mb-2" min="0" step="1" onkeydown="blockE(event)" required>
                <textarea name="edit_leiras" placeholder="Leírás" class="form-control mb-2" required></textarea>
                <input type="number" name="edit_kaloria" placeholder="Kalória" class="form-control mb-2" min="0" step="1" onkeydown="blockE(event)" required>
                <textarea name="edit_osszetevok" placeholder="Összetevők" class="form-control mb-2" required></textarea>
                <textarea name="edit_allergenek" placeholder="Allergének" class="form-control mb-2" required></textarea><select name="edit_kategoria_id" class="form-select mb-2" required>
                    <option value="">Válassz kategóriát</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['id']) ?>">
                            <?= htmlspecialchars($kategoria['kategoria_nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="mb-3">
                    <input type="file" name="edit_kepek_url" accept="image/*" class="form-control mb-2">
                    <small class="text-muted">Új kép feltöltése esetén a régi törlődik, és az étel neve alapján neveződik el.</small>
                    <div id="currentKepInfo" class="mt-2"></div>
                </div>
                <button type="submit" data-operation="edit" class="btn btn-primary">Szerkesztés</button>
            </div>

            <!-- Törlés űrlap -->
            <div id="delete-form" class="form-section" style="display:none;">
                <h3>Törlés</h3>
                <select name="delete_etel" class="form-select mb-2" required>
                    <option value="">Válassz ételt</option>
                    <?php foreach ($etelek as $etel): ?>
                        <option value="<?= htmlspecialchars($etel['id']) ?>">
                            <?= htmlspecialchars($etel['nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" data-operation="delete" class="btn btn-danger mb-3">Törlés</button>
            </div>
        </form>
    </div>

    <!-- Felhasználó szerkesztése -->
    <div class="container mt-5">
        <h2>Felhasználó szerkesztése</h2>
        <table class="table table-dark table-bordered">
            <thead>
                <tr>
                    <th>Felhasználónév</th>
                    <th>Jogosultság</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($felhasznalok as $felhasznalo): ?>
                    <tr>
                        <td><?= htmlspecialchars($felhasznalo['felhasznalo_nev']) ?></td>
                        <td>
                            <?php
                            switch ($felhasznalo['jog_szint']) {
                                case 0: echo "Felhasználó"; break;
                                case 1: echo "Admin"; break;
                                case 2: echo "Dolgozó"; break;
                                default: echo "Ismeretlen";
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($felhasznalo['jog_szint'] != 1): ?>
                                <button class="btn btn-primary" onclick="editUser(<?= $felhasznalo['id'] ?>, <?= $felhasznalo['jog_szint'] ?>)">Változtatás</button>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Változtatás</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-grid gap-2 col-6 mx-auto">
        <a class="btn btn-secondary" href="dolgozoi_felulet.php">Dolgozói felület</a>
    </div>

    <!-- Modalok -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Felhasználó szerkesztése</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="POST">
                        <input type="hidden" name="editUserId" id="editUserId">
                        <input type="hidden" name="currentJogSzint" id="currentJogSzint">
                        <input type="hidden" name="operation" value="edit_user">
                        <div class="form-group">
                            <p><strong>Jelenlegi jogosultság:</strong> <span id="currentJogSzintText"></span></p>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="radio" name="jog_szint" value="0" id="jogSzintFelhasznalo">
                                Felhasználói jogosultság
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="radio" name="jog_szint" value="2" id="jogSzintDolgozo">
                                Dolgozói jogosultság
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                    <button type="button" class="btn btn-primary" onclick="saveUserChanges()">Mentés</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Megerősítés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Biztosan szeretnéd végrehajtani a változtatásokat?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Igen</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelector('select[name="edit_etel"]').addEventListener('change', function() {
        const etelId = this.value;
        if (!etelId) return;
        fetch(`admin_felulet.php?action=get_etel&id=${etelId}`)
            .then(response => response.json())
            .then(data => {
                document.querySelector('input[name="edit_nev"]').value = data.nev;
                document.querySelector('input[name="edit_egyseg_ar"]').value = data.egyseg_ar;
                document.querySelector('textarea[name="edit_leiras"]').value = data.leiras;
                document.querySelector('input[name="edit_kaloria"]').value = data.kaloria;
                document.querySelector('textarea[name="edit_osszetevok"]').value = data.osszetevok;
                document.querySelector('textarea[name="edit_allergenek"]').value = data.allergenek;
                document.querySelector('select[name="edit_kategoria_id"]').value = data.kategoria_id;
                const kepInfoDiv = document.getElementById('currentKepInfo');
                kepInfoDiv.innerHTML = data.kep_url 
                    ? `<strong>Aktuális kép:</strong><br><img src="../kepek/${data.kategoria_id}/${data.kep_url}" style="max-height: 100px;" class="img-thumbnail mt-2"><br><small>${data.kep_url}</small>`
                    : '<div class="text-muted">Nincs kép</div>';
            });
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('operation').addEventListener('change', function () {
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = this.value === section.id.replace('-form', '') ? 'block' : 'none';
            });
        });

        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        document.querySelectorAll('[data-operation]').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const operation = this.getAttribute('data-operation');
                const form = document.getElementById(operation + '-form');
                const requiredInputs = form.querySelectorAll('[required]');
                let isValid = true;
                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                if (isValid) {
                    confirmationModal.show();
                    document.getElementById('confirmAction').onclick = () => {
                        confirmationModal.hide();
                        document.getElementById('operation').value = operation;
                        document.getElementById('adminForm').submit();
                    };
                }
            });
        });
    });

    function editUser(userId, currentJogSzint) {
        document.getElementById('editUserId').value = userId;
        document.getElementById('currentJogSzint').value = currentJogSzint;

        // Jelenlegi jogosultság szöveges megjelenítése
        const jogSzintText = document.getElementById('currentJogSzintText');
        switch (currentJogSzint) {
            case 0:
                jogSzintText.textContent = 'Felhasználó';
                break;
            case 2:
                jogSzintText.textContent = 'Dolgozó';
                break;
            default:
                jogSzintText.textContent = 'Ismeretlen';
        }

        // Rádiógombok letiltása, ha a jogosultság már be van állítva
        const felhasznaloRadio = document.getElementById('jogSzintFelhasznalo');
        const dolgozoRadio = document.getElementById('jogSzintDolgozo');
        
        felhasznaloRadio.disabled = (currentJogSzint === 0);
        dolgozoRadio.disabled = (currentJogSzint === 2);

        // Ha az egyik opció letiltva van, a másikat alapértelmezetten kiválasztjuk
        if (currentJogSzint === 0) {
            dolgozoRadio.checked = true;
        } else if (currentJogSzint === 2) {
            felhasznaloRadio.checked = true;
        }

        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    function saveUserChanges() {
        const selectedRadio = document.querySelector('input[name="jog_szint"]:checked');
        if (!selectedRadio) {
            alert("Kérjük, válasszon jogosultsági szintet!");
            return;
        }
        document.getElementById('editUserForm').submit();
    }

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    function blockE(event) {
    if (event.key === 'e' || event.key === 'E') {
        event.preventDefault();
    }
}


    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>