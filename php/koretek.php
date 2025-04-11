<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../kepek/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <title>FlavorWave - Köretek</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/kategoriaelemek.css">
    <link rel="stylesheet" href="../css/fooldal/ujfooldal.css">
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>

<body>
    <?php
        include './navbar.php';
    ?>

    <div class="content-wrapper">
        <main>
            <div class="title-container">
                <h1 class="page-title" id="cim">Köretek</h1>
            </div>
            <?php

            $kategoriak_sql = "SELECT * FROM etel WHERE kategoria_id = 6;";
            $kategoriak = adatokLekerdezese($kategoriak_sql);
            if (is_array($kategoriak)) {
                echo '<div class="flex-grid">';
                foreach ($kategoriak as $k) {
                    echo '
                    <div class="flex-col">
                        <div class="menu__option" data-aos="fade-up" data-aos-delay="' . (100 * (array_search($k, $kategoriak) + 1)) . '">
                            <div class="image-wrapper">
                                <img src="../kepek/6/' . htmlspecialchars($k['kep_url']) . '" alt="' . htmlspecialchars($k['nev']) . '">
                            </div>
                            <h2>' . htmlspecialchars($k['nev']) . '</h2>
                            <div class="title-underline"></div>
                            <button class="order-btn details-btn" onclick="openCustomModal(\'modal-' . htmlspecialchars($k['id']) . '\')">Részletek</button>
                        </div>
                    </div>

                    <div class="custom-modal" id="modal-' . htmlspecialchars($k['id']) . '">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <h5 class="custom-modal-title">' . htmlspecialchars($k['nev']) . ' - Részletek</h5>
                                <button type="button" class="custom-close-btn" onclick="closeCustomModal(\'modal-' . htmlspecialchars($k['id']) . '\')">×</button>
                            </div>
                            <div class="custom-modal-body">
                                <img src="../kepek/6/' . htmlspecialchars($k['kep_url']) . '" class="img-fluid mb-3" alt="' . htmlspecialchars($k['nev']) . '">
                                <p><strong>Kalória:</strong> ' . htmlspecialchars($k['kaloria']) . ' kcal</p>
                                <p><strong>Összetevők:</strong> ' . htmlspecialchars($k['osszetevok']) . '</p>
                                <p><strong>Allergének:</strong> ' . htmlspecialchars($k['allergenek']) . '</p>
                                <p><strong>Ár:</strong> ' . htmlspecialchars($k['egyseg_ar']) . ' Ft</p>
                            </div>
                            <div class="custom-modal-footer">
                                <button type="button" class="order-btn add-to-cart" data-item-id="' . htmlspecialchars($k["id"]) . '" data-item="' . htmlspecialchars($k['nev']) . '" data-image="../kepek/Koretek/' . htmlspecialchars($k['kep_url']) . '" onclick="addToCartAndClose(\'modal-' . htmlspecialchars($k['id']) . '\', \'' . htmlspecialchars($k["id"]) . '\')">Kosárba rakás</button>
                                <button type="button" class="order-btn close-btn" onclick="closeCustomModal(\'modal-' . htmlspecialchars($k['id']) . '\')">Bezárás</button>
                            </div>
                        </div>
                    </div>';
                }
                echo '</div>';
            } else {
                echo "<p>Ehhez a kategóriához nem tartozik étel!</p>";
            }
            ?>
        </main>

        <!-- Toast értesítés HTML kód -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toast-added" class="custom-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Sikeresen hozzáadva a kosárhoz!
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" onclick="closeToast()">×</button>
                </div>
            </div>
        </div>

        <!-- Animációhoz szükséges rejtett elem -->
        <div id="cart-animation-item" class="cart-animation-item"></div>

        <div class="footer">
            <div class="footer-container">
                <ul class="footer-links">
                    <li><a href="../html/rolunk.html">Rólunk</a></li>
                    <li><a href="../html/kapcsolatok.html">Kapcsolat</a></li>
                    <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
                </ul>
                <div class="footer-socials">
                    <a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-copy">
                    © 2024 FlavorWave - Minden jog fenntartva.
                </div>
            </div>
        </div>
    </div>

    <script src="../js/navbar.js"></script>
    <script src="../js/kosarSzamlalo.js">

    </script>
</body>
</html>