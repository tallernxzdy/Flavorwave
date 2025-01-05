<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <title>FlavorWave - Hamburgerek</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/kategoriaelemek.css">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
    <nav>
    <div class="logo">
        <a href="kezdolap.php" class="logo">
        <img src="../kepek/logo.png" alt="Flavorwave logó" class="logo-img">
        <h1>FlavorWave</h1>
        </a>
    </div>
    <ul>
        <li><a href="kategoria.php">Kategóriák</a></li>
        <li><a href="menu.php">Menü</a></li>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
        <li><a href="admin_felulet.php">Admin felület</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION["username"])): ?>
        <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
        <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <?php endif; ?>
    </ul>
    <div class="hamburger">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>
    </nav>

    <div class="menubar">
    <ul>
        <li><a href="menu.php">Menü</a></li>
        <?php if (isset($_SESSION["jog_szint"]) && $_SESSION["jog_szint"] == 1): ?>
        <li><a href="admin_felulet.php">Admin felület</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION["username"])): ?>
        <li><a href="kijelentkezes.php">Kijelentkezés</a></li>
        <?php else: ?>
        <li><a href="bejelentkezes.php">Bejelentkezés</a></li>
        <?php endif; ?>
    </ul>
    </div>

    <?php
        include './adatbazisra_csatlakozas.php';

        $kategoriak_sql = "SELECT * FROM etel WHERE kategoria_id = 3;";
        $kategoriak = adatokLekerdezese($kategoriak_sql);
        if(is_array($kategoriak)){
        foreach ($kategoriak as $k) {
            echo '<div class="container my-5">
                <div class="row g-4">
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card hover-card" data-bs-toggle="modal" data-bs-target="#modal-' . htmlspecialchars($k['id']) . '">
                            <img src="' . htmlspecialchars($k['kep_url']) . '" class="card-img-top" alt="Étel kép">
                            <div class="card-body text-center">
                                <h5 class="card-title">' . htmlspecialchars($k['nev']) . '</h5>
                                <p class="card-text">' . htmlspecialchars($k['leiras']) . '</p>
                                <button class="modern-btn add-to-cart" data-item="' . htmlspecialchars($k['nev']) . '">Kosárba rakás</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade custom-fade" id="modal-' . htmlspecialchars($k['id']) . '" tabindex="-1" aria-labelledby="modalLabel-' . htmlspecialchars($k['id']) . '" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-' . htmlspecialchars($k['id']) . '">' . htmlspecialchars($k['nev']) . ' - Részletek</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="' . htmlspecialchars($k['kep_url']) . '" class="img-fluid mb-3" alt="Étel kép">
                            <p><strong>Kalória:</strong> ' . htmlspecialchars($k['kaloria']) . ' kcal</p>
                            <p><strong>Összetevők:</strong> ' . htmlspecialchars($k['osszetevok']) . '</p>
                            <p><strong>Allergének:</strong> ' . htmlspecialchars($k['allergenek']) . '</p>
                            <p><strong>Ár:</strong> ' . htmlspecialchars($k['egyseg_ar']) . ' Ft</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modern-btn close-btn" data-bs-dismiss="modal">Bezárás</button>
                        </div>
                    </div>
                </div>
            </div>';
        }
    }
        else {
            echo "<p>Ehez a kategóriához nem tartozik étel!</p>";
        }

    ?>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
    // Kosárba rakás gomb kattintása
    document.querySelectorAll(".add-to-cart").forEach(function (button) {
        button.addEventListener("click", function (event) {
        event.stopPropagation(); // Ne nyissa meg a modált
        const toastEl = document.getElementById("toast-added");
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        });
    });

    // Kártya kattintás logikája
    document.querySelectorAll(".hover-card").forEach(function (card) {
        card.addEventListener("click", function () {
        // A modális automatikusan megnyílik a data-bs-toggle attribútum miatt
        });
    });
    });


    </script>

    
    


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
            &copy; 2024 FlavorWave - Minden jog fenntartva.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>