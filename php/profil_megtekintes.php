<?php
session_start();
include './adatbazisra_csatlakozas.php';

$logged_in = isset($_SESSION['felhasznalo_id']);
$user_data = null;

if ($logged_in) {
    $stmt = $conn->prepare("SELECT * FROM felhasznalo WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['felhasznalo_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    }
    $stmt->close();
}

// Alert üzenetek kezelése
$alert = '';
if (isset($_GET['success'])) {
    $alert = '<div class="alert alert-success mt-3">Sikeresen módosítva!</div>';
} elseif (isset($_GET['error'])) {
    $alert = '<div class="alert alert-danger mt-3">Hiba történt a módosítás során!</div>';
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilom - FlavorWave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/profilom.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include './navbar.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
        </div>

        <div class="profile-content">
            <?php if ($logged_in && $user_data): ?>
                <?php echo $alert; ?>
                <form id="profile-form" method="POST" action="profil_frissites.php" onsubmit="return false;">
                    <div class="profile-card">
                        <div class="user-info">
                            <!-- Teljes név -->
                            <div class="info-item" id="teljes_nev_item">
                                <span class="info-label"><i class="fas fa-user"></i> Teljes név:</span>
                                <span class="info-value" id="teljes_nev_value"><?php echo htmlspecialchars($user_data['Teljes_nev'] ?: 'Nincs megadva'); ?></span>
                                <input type="text" class="form-control edit-input" name="teljes_nev" value="<?php echo htmlspecialchars($user_data['Teljes_nev']); ?>" style="display: none;" onblur="hideInput('teljes_nev')">
                                <i class="fas fa-pencil-alt edit-icon" onclick="toggleEdit('teljes_nev')"></i>
                            </div>
                            <!-- Felhasználónév -->
                            <div class="info-item" id="felhasznalo_nev_item">
                                <span class="info-label"><i class="fas fa-at"></i> Felhasználónév:</span>
                                <span class="info-value" id="felhasznalo_nev_value"><?php echo htmlspecialchars($user_data['felhasznalo_nev'] ?: 'Nincs megadva'); ?></span>
                                <input type="text" class="form-control edit-input" name="felhasznalo_nev" value="<?php echo htmlspecialchars($user_data['felhasznalo_nev']); ?>" style="display: none;" onblur="hideInput('felhasznalo_nev')">
                                <i class="fas fa-pencil-alt edit-icon" onclick="toggleEdit('felhasznalo_nev')"></i>
                            </div>
                            <!-- Email cím -->
                            <div class="info-item" id="email_cim_item">
                                <span class="info-label"><i class="fas fa-envelope"></i> Email cím:</span>
                                <span class="info-value" id="email_cim_value"><?php echo htmlspecialchars($user_data['email_cim'] ?: 'Nincs megadva'); ?></span>
                                <input type="email" class="form-control edit-input" name="email_cim" value="<?php echo htmlspecialchars($user_data['email_cim']); ?>" style="display: none;" onblur="hideInput('email_cim')">
                                <i class="fas fa-pencil-alt edit-icon" onclick="toggleEdit('email_cim')"></i>
                            </div>
                            <!-- Telefonszám -->
                            <div class="info-item" id="tel_szam_item">
                                <span class="info-label"><i class="fas fa-phone"></i> Telefonszám:</span>
                                <span class="info-value" id="tel_szam_value"><?php echo htmlspecialchars($user_data['tel_szam'] ?: 'Nincs megadva'); ?></span>
                                <input type="text" class="form-control edit-input" name="tel_szam" value="<?php echo htmlspecialchars($user_data['tel_szam']); ?>" style="display: none;" onblur="hideInput('tel_szam')">
                                <i class="fas fa-pencil-alt edit-icon" onclick="toggleEdit('tel_szam')"></i>
                            </div>
                            <!-- Lakcím -->
                            <div class="info-item" id="lakcim_item">
                                <span class="info-label"><i class="fas fa-home"></i> Lakcím:</span>
                                <span class="info-value" id="lakcim_value"><?php echo htmlspecialchars($user_data['lakcim'] ?: 'Nincs megadva'); ?></span>
                                <input type="text" class="form-control edit-input" name="lakcim" value="<?php echo htmlspecialchars($user_data['lakcim']); ?>" style="display: none;" onblur="hideInput('lakcim')">
                                <i class="fas fa-pencil-alt edit-icon" onclick="toggleEdit('lakcim')"></i>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success mt-3" onclick="validateAndShowModal()"><i class="fas fa-save"></i> Mentés</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="not-logged-in">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle"></i> A profil oldal megtekintéséhez be kell jelentkeznie!
                    </div>
                    <div class="d-grid gap-2">
                        <a href="bejelentkezes.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Bejelentkezés
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal ablak -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Megerősítés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="resetForm()"></button>
                </div>
                <div class="modal-body">
                    Biztosan el szeretné menteni a változtatásokat?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetForm()">Nem</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Igen</button>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-container">
            <ul class="footer-links">
                <li><a href="../html/rolunk.html">Rólunk</a></li>
                <li><a href="../html/kapcsolatok.html">Kapcsolat</a></li>
                <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
            </ul>
            <div class="footer-socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="footer-copy">
                © 2024 FlavorWave - Minden jog fenntartva.
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Eredeti adatok tárolása
        const originalData = {
            teljes_nev: '<?php echo htmlspecialchars($user_data['Teljes_nev'] ?: ''); ?>',
            felhasznalo_nev: '<?php echo htmlspecialchars($user_data['felhasznalo_nev'] ?: ''); ?>',
            email_cim: '<?php echo htmlspecialchars($user_data['email_cim'] ?: ''); ?>',
            tel_szam: '<?php echo htmlspecialchars($user_data['tel_szam'] ?: ''); ?>',
            lakcim: '<?php echo htmlspecialchars($user_data['lakcim'] ?: ''); ?>'
        };

        function toggleEdit(field) {
            const valueSpan = document.getElementById(`${field}_value`);
            const inputField = document.querySelector(`input[name="${field}"]`);

            // Ha az input látható, akkor elrejtjük és mentjük az értéket
            if (inputField.style.display === "block") {
                inputField.style.display = "none";
                valueSpan.style.display = "inline";
                valueSpan.textContent = inputField.value || 'Nincs megadva';
            }
            // Ha az input el van rejtve, akkor megjelenítjük
            else {
                inputField.style.display = "block";
                valueSpan.style.display = "none";
                inputField.focus();
            }
        }

        function hideInput(field) {
            const valueSpan = document.getElementById(`${field}_value`);
            const inputField = document.querySelector(`input[name="${field}"]`);

            inputField.style.display = "none";
            valueSpan.style.display = "inline";
            valueSpan.textContent = inputField.value || 'Nincs megadva';
        }

        function validateAndShowModal() {
            const email = document.querySelector('input[name="email_cim"]').value;
            const telSzam = document.querySelector('input[name="tel_szam"]').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const telRegex = /^\+?[0-9]{10,12}$/; // Példa: +36201234567 vagy 06201234567

            if (email && !emailRegex.test(email)) {
                showAlert('danger', 'Helytelen email cím formátum!');
                return;
            }
            if (telSzam && !telRegex.test(telSzam)) {
                showAlert('danger', 'Helytelen telefonszám formátum! (Pl. +36201234567)');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        function submitForm() {
            document.getElementById('profile-form').onsubmit = function() {
                return true;
            }; // Engedélyezzük az űrlap elküldését
            document.getElementById('profile-form').submit();
        }

        function resetForm() {
            Object.keys(originalData).forEach(field => {
                const valueSpan = document.getElementById(`${field}_value`);
                const inputField = document.querySelector(`input[name="${field}"]`);
                valueSpan.textContent = originalData[field] || 'Nincs megadva';
                inputField.value = originalData[field];
                valueSpan.style.display = "inline";
                inputField.style.display = "none";
            });
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} mt-3`;
            alertDiv.textContent = message;
            document.querySelector('.profile-content').prepend(alertDiv);
            setTimeout(() => alertDiv.remove(), 3000); // 3 másodperc után eltűnik
        }
    </script>
</body>

</html>