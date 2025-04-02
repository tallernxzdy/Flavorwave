<?php
session_start();
include './adatbazisra_csatlakozas.php';

$logged_in = isset($_SESSION['felhasznalo_id']);
$user_data = null;

if ($logged_in) {
    // Adatbázisból lekérjük a felhasználó adatait
    $stmt = $conn->prepare("SELECT * FROM felhasznalo WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['felhasznalo_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    }
    $stmt->close();
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

    <?php 
        include './navbar.php'; 
    ?>
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
        </div>
        
        <div class="profile-content">
            <?php if ($logged_in && $user_data): ?>
                <div class="profile-card">
                    <div class="user-info">
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-user"></i> Teljes név:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['Teljes_nev']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-at"></i> Felhasználónév:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['felhasznalo_nev']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-envelope"></i> Email cím:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['email_cim']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-phone"></i> Telefonszám:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['tel_szam']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-home"></i> Lakcím:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user_data['lakcim']); ?></span>
                        </div>
                    </div>
                </div>
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
    </div>

    <script src="../js/navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>