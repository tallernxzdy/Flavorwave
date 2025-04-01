<?php
session_start();
include 'adatbazisra_csatlakozas.php';

// Jogosultság ellenőrzése
if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] != 1) {
    header('Location: bejelentkezes.php');
    exit;
}

// API mód - Csak GET kérések kezelése
if (isset($_GET['action']) && $_GET['action'] === 'get_etel' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Jogosultság ellenőrzése
    if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] != 1) {
        http_response_code(403);
        exit(json_encode(['error' => 'Nincs jogosultság!']));
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Érvénytelen ID!']));
    }

    $id = (int)$_GET['id'];

    try {
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
        
    } catch (Exception $e) {
        http_response_code(500);
        exit(json_encode(['error' => 'Adatbázis hiba: ' . $e->getMessage()]));
    }
}

// Alap admin felület további része (eredeti kód)
// Jogosultság ellenőrzése
if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['jog_szint'] != 1) {
    header('Location: bejelentkezes.php');
    exit;
}



$message = "";

// Kategóriák lekérdezése
$kategoriak = adatokLekerdezese("SELECT id, kategoria_nev FROM kategoria");
if (!is_array($kategoriak)) {
    $message = "<div class='alert alert-warning'>Hiba a kategóriák lekérdezése során: $kategoriak</div>";
}

// Ételek lekérdezése
$etelek = adatokLekerdezese("SELECT id, nev FROM etel");
if (!is_array($etelek)) {
    $message = "<div class='alert alert-warning'>Hiba az ételek lekérdezése során: $etelek</div>";
}

// Felhasználók lekérdezése a jogosultság megváltoztatásához
$felhasznalok = [];
$felhasznalokLekerdezese = adatokLekerdezese("SELECT id, felhasznalo_nev, jog_szint FROM felhasznalo");
if (is_array($felhasznalokLekerdezese)) {
    $felhasznalok = $felhasznalokLekerdezese;
} else {
    $message = "<div class='alert alert-warning'>Hiba a felhasználók lekérdezése során: $felhasznalokLekerdezese</div>";
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
        $kaloria = $_POST['kaloria'];
        $osszetevok = $_POST['osszetevok'];
        $allergenek = $_POST['allergenek'];

        $kategoria = adatokLekerdezese("SELECT kategoria_nev FROM kategoria WHERE id = ?", ['i', $kategoria_id]);
        if (!is_array($kategoria) || empty($kategoria)) {
            $message = "<div class='alert alert-warning'>Hiba a kategória lekérdezése során!</div>";
        } else {
            $kategoria_nev = strtolower($kategoria[0]['kategoria_nev']);
            $kep_url = "";
            if (isset($_FILES['kepek_url']['name']) && $_FILES['kepek_url']['name'] !== "") {
                $target_dir = "../kepek/" . $kategoria_nev . "/";
                $kep_nev = $_POST['kep_nev'] ?? uniqid();
                $kiterjesztes = pathinfo($_FILES['kepek_url']['name'], PATHINFO_EXTENSION);
                $uniqueName = $kep_nev . '.' . $kiterjesztes;
                $target_file = $target_dir . $uniqueName;

                if (file_exists($target_file)) {
                    $message = "<div class='alert alert-warning'>Ez a képnév már foglalt!</div>";
                } else {
                    if (move_uploaded_file($_FILES['kepek_url']['tmp_name'], $target_file)) {
                        $kep_url = "$kategoria_nev/$uniqueName";
                    } else {
                        $message = "<div class='alert alert-warning'>Hiba a kép feltöltése során!</div>";
                    }
                }
            }

            $muvelet = "INSERT INTO etel (nev, egyseg_ar, leiras, kategoria_id, kep_url, kaloria, osszetevok, allergenek) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $parameterek = ['ssssssss', $nev, $egyseg_ar, $leiras, $kategoria_id, $kep_url, $kaloria, $osszetevok, $allergenek];
            $result = adatokValtoztatasa($muvelet, $parameterek);
            $message = "<div class='alert alert-success'>Étel sikeresen hozzáadva!</div>";
        }
    }

    // Szerkesztés (képkezelés nélkül)
    if ($operation === 'edit') {
        $id = $_POST['edit_etel'];
        $nev = $_POST['edit_nev'];
        $egyseg_ar = $_POST['edit_egyseg_ar'];
        $leiras = $_POST['edit_leiras'];
        $kategoria_id = $_POST['edit_kategoria_id'];
        $kaloria = $_POST['edit_kaloria'];
        $osszetevok = $_POST['edit_osszetevok'];
        $allergenek = $_POST['edit_allergenek'];

        $muvelet = "UPDATE etel SET nev = ?, egyseg_ar = ?, leiras = ?, kategoria_id = ?, kaloria = ?, osszetevok = ?, allergenek = ? WHERE id = ?";
        $parameterek = ['sssssssi', $nev, $egyseg_ar, $leiras, $kategoria_id, $kaloria, $osszetevok, $allergenek, $id];
        $result = adatokValtoztatasa($muvelet, $parameterek);
        $message = "<div class='alert alert-success'>Étel sikeresen szerkesztve!</div>";
    }

    // Törlés
    if ($operation === 'delete') {
        $id = $_POST['delete_etel'];
        $etel = adatokLekerdezese("SELECT kep_url FROM etel WHERE id = ?", ['i', $id]);
        if (is_array($etel) && count($etel) > 0 && !empty($etel[0]['kep_url']) && file_exists("../kepek/" . $etel[0]['kep_url'])) {
            unlink("../kepek/" . $etel[0]['kep_url']);
        }

        $result = adatokTorlese($id);
        $message = "<div class='alert alert-success'>Étel sikeresen törölve!</div>";
    }

    // Felhasználó szerkesztése
    if ($operation === 'edit_user') {
        $userId = $_POST['editUserId'];
        $jogSzint = $_POST['jog_szint'];

        $checkSql = "SELECT jog_szint FROM felhasznalo WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $checkStmt->bind_result($currentJogSzint);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($currentJogSzint == $jogSzint) {
            $message = "<div class='alert alert-warning'>A felhasználó már rendelkezik ezzel a jogosultsági szinttel!</div>";
        } else {
            $sql = "UPDATE felhasznalo SET jog_szint = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $jogSzint, $userId);

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Felhasználó jogosultsága sikeresen frissítve!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Hiba történt a frissítés során!</div>";
            }

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
                <input type="number" name="egyseg_ar" placeholder="Egységár" class="form-control mb-2" required>
                <textarea name="leiras" placeholder="Leírás" class="form-control mb-2" required></textarea>
                <input type="number" name="kaloria" placeholder="Kalória" class="form-control mb-2" required>
                <textarea name="osszetevok" placeholder="Összetevők" class="form-control mb-2" required></textarea>
                <textarea name="allergenek" placeholder="Allergének" class="form-control mb-2" required></textarea>
                <select name="kategoria_id" class="form-select mb-2" required>
                    <option value="">Válassz kategóriát</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['id']) ?>">
                            <?= htmlspecialchars($kategoria['kategoria_nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="kep_nev" placeholder="Kép neve (kiterjesztés nélkül)" class="form-control mb-2" required>
                <input type="file" name="kepek_url" accept="image/*" class="form-control mb-2" required>
                <button type="submit" data-operation="add" class="btn btn-primary">Hozzáadás</button>
            </div>

            <!-- Szerkesztés űrlap (kép nélkül) -->
            <div id="edit-form" class="form-section" style="display:none;">
                <h3>Szerkesztés</h3>
                <select name="edit_etel" class="form-select mb-2" id="editEtelSelect">
                    <option value="">Válassz ételt</option>
                    <?php foreach ($etelek as $etel): ?>
                        <option value="<?= htmlspecialchars($etel['id']) ?>">
                            <?= htmlspecialchars($etel['nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="edit_nev" placeholder="Név" class="form-control mb-2" required>
                <input type="number" name="edit_egyseg_ar" placeholder="Egységár" class="form-control mb-2" required>
                <textarea name="edit_leiras" placeholder="Leírás" class="form-control mb-2" required></textarea>
                <input type="number" name="edit_kaloria" placeholder="Kalória" class="form-control mb-2" required>
                <textarea name="edit_osszetevok" placeholder="Összetevők" class="form-control mb-2" required></textarea>
                <textarea name="edit_allergenek" placeholder="Allergének" class="form-control mb-2" required></textarea>
                <select name="edit_kategoria_id" class="form-select mb-2" required>
                    <option value="">Válassz kategóriát</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= htmlspecialchars($kategoria['id']) ?>">
                            <?= htmlspecialchars($kategoria['kategoria_nev']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
                <button type="submit" data-operation="delete" class="btn btn-danger">Törlés</button>
            </div>
        </form>
    </div>

    <!-- Felhasználó szerkesztése -->
    <div class="container mt-5">
        <h2>Felhasználó szerkesztése</h2>
        <table class="table table-bordered">
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
                                case 0:
                                    echo "Felhasználó";
                                    break;
                                case 1:
                                    echo "Admin";
                                    break;
                                case 2:
                                    echo "Dolgozó";
                                    break;
                                default:
                                    echo "Ismeretlen";
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

    <!-- Dolgozói felületre ugrás -->
    <br>
    <div class="d-grid gap-2 col-6 mx-auto">
        <a class="btn btn-secondary" href="dolgozoi_felulet.php">Dolgozói felület</a>
    </div>

    <!-- Modal a jogosultság módosításához -->
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
                            <label>Felhasználói jogosultságra váltás:</label>
                            <input type="radio" name="jog_szint" value="0">
                        </div>
                        <div class="form-group">
                            <label>Dolgozói jogosultságra váltás:</label>
                            <input type="radio" name="jog_szint" value="2">
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

    <!-- Megerősítés Modal -->
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

    <!-- JavaScript -->
    <script>
    // A szerkesztési űrlap select elemének figyelése
    document.querySelector('select[name="edit_etel"]').addEventListener('change', function() {
        const etelId = this.value;
        if (!etelId) return;

        // A fetch URL-t módosítsd erre
        fetch(`admin_felulet.php?action=get_etel&id=${etelId}`)
            .then(response => {
                if (!response.ok) throw new Error('Hiba a lekérdezésben');
                return response.json();
            })
            .then(data => {
                // Mezők feltöltése
                document.querySelector('input[name="edit_nev"]').value = data.nev;
                document.querySelector('input[name="edit_egyseg_ar"]').value = data.egyseg_ar;
                document.querySelector('textarea[name="edit_leiras"]').value = data.leiras;
                document.querySelector('input[name="edit_kaloria"]').value = data.kaloria;
                document.querySelector('textarea[name="edit_osszetevok"]').value = data.osszetevok;
                document.querySelector('textarea[name="edit_allergenek"]').value = data.allergenek;
                
                // Kategória beállítása
                const kategoriaSelect = document.querySelector('select[name="edit_kategoria_id"]');
                Array.from(kategoriaSelect.options).forEach(option => {
                    option.selected = (option.value == data.kategoria_id);
                });
            })
            .catch(error => {
                console.error('Hiba:', error);
                alert('Nem sikerült betölteni az étel adatait');
            });
    });

        document.addEventListener('DOMContentLoaded', function () {
            // Műveletváltás űrlap megjelenítése
            document.getElementById('operation').addEventListener('change', function () {
                const sections = document.querySelectorAll('.form-section');
                sections.forEach(section => {
                    section.style.display = 'none';
                });
                if (this.value) {
                    const activeSection = document.getElementById(this.value + '-form');
                    activeSection.style.display = 'block';
                }
            });

            // Modal megerősítés és valós idejű input ellenőrzés
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            const confirmActionButton = document.getElementById('confirmAction');
            let currentOperation = null;

            document.querySelectorAll('[data-operation]').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    currentOperation = this.getAttribute('data-operation');
                    const activeForm = document.getElementById(currentOperation + '-form');

                    if (activeForm && activeForm.style.display !== 'none') {
                        const requiredInputs = activeForm.querySelectorAll('input[required], textarea[required], select[required]');
                        const fileInput = activeForm.querySelector('input[type="file"]');
                        const kepNevInput = activeForm.querySelector(currentOperation === 'add' ? 'input[name="kep_nev"]' : null);
                        let isValid = true;

                        requiredInputs.forEach(input => {
                            if (!input.value.trim()) {
                                isValid = false;
                                input.classList.add('is-invalid');
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });

                        if (fileInput && fileInput.files.length > 0 && kepNevInput && !kepNevInput.value.trim()) {
                            isValid = false;
                            kepNevInput.classList.add('is-invalid');
                        } else if (kepNevInput) {
                            kepNevInput.classList.remove('is-invalid');
                        }

                        if (!isValid) {
                            return;
                        }
                    } else {
                        console.log('Nincs aktív űrlap, vagy nem látható:', activeForm);
                        return;
                    }

                    confirmationModal.show();
                });
            });

            confirmActionButton.addEventListener('click', function () {
                confirmationModal.hide();
                if (currentOperation) {
                    document.getElementById('operation').value = currentOperation;
                    document.getElementById('adminForm').submit();
                }
            });

            // Valós idejű ellenőrzés az input mezőkre
            const allInputs = document.querySelectorAll('input[required], textarea[required], select[required]');
            allInputs.forEach(input => {
                input.addEventListener('input', function () {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    } else {
                        this.classList.add('is-invalid');
                    }
                });
            });

            // Kategóriák szerinti fájlfeltöltés (csak hozzáadáshoz)
            const addKategoriaSelect = document.querySelector('select[name="kategoria_id"]');
            const addFileInput = document.querySelector('input[name="kepek_url"]');

            if (addKategoriaSelect && addFileInput) {
                addKategoriaSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const kategoriaNev = selectedOption.textContent.trim().toLowerCase();
                    if (kategoriaNev) {
                        addFileInput.setAttribute('accept', `image/${kategoriaNev}/*`);
                    } else {
                        addFileInput.setAttribute('accept', 'image/*');
                    }
                });
            }
        });

        // Felhasználó szerkesztés modal
        function editUser(userId, currentJogSzint) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('currentJogSzint').value = currentJogSzint;
            const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editUserModal.show();
        }

        function saveUserChanges() {
            const currentJogSzint = parseInt(document.getElementById('currentJogSzint').value, 10);
            const selectedRadio = document.querySelector('input[name="jog_szint"]:checked');

            if (!selectedRadio) {
                alert("Kérjük, válasszon jogosultsági szintet!");
                return;
            }

            const selectedJogSzint = parseInt(selectedRadio.value, 10);

            if (currentJogSzint === selectedJogSzint) {
                alert("A felhasználó már rendelkezik ezzel a jogosultsági szinttel!");
                return;
            }

            document.getElementById('editUserForm').submit();
        }

        // History replace state (űrlap újraküldés megakadályozása)
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>