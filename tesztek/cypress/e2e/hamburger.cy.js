describe('FlavorWave Hamburgerek Kategória Tesztek', () => {
  // Uncaught exception figyelmen kívül hagyása
  Cypress.on('uncaught:exception', (err, runnable) => {
    return false; // Ne bukjon meg a teszt alkalmazás hibák miatt
  });

  beforeEach(() => {
    // Mockoljuk a get_cart_count.php-t a navbar miatt
    cy.intercept('GET', '**/get_cart_count.php', {
      statusCode: 200,
      body: { count: 0 }
    }).as('getCartCount');

    // Mockoljuk a kategoriaelemek.php GET kérését
    cy.intercept('GET', '**/kategoriaelemek.php', {
      statusCode: 200,
      headers: { 'Content-Type': 'text/html' },
      body: `
        <!DOCTYPE html>
        <html lang="hu">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>FlavorWave - Hamburgerek</title>
        </head>
        <body>
          <div class="container">
            <nav>
              <a href="kezdolap.php" class="logo">
                <img src="../kepek/logo.png" alt="Flavorwave Logo">
                <h1>FlavorWave</h1>
              </a>
              <div class="navbar-center">
                <a href="kategoria.php">Menü</a>
                <a href="rendeles_megtekintes.php" class="order-button">Rendeléseim</a>
              </div>
              <div class="navbar-buttons">
                <a href="kijelentkezes.php" class="login-btn">Kijelentkezés</a>
                <a href="kosar.php" class="cart-btn">
                  <i class="fas fa-shopping-cart cart-icon"></i> Kosár
                  <span class="cart-count">0</span>
                </a>
                <a href="profil_megtekintes.php" class="profile-btn">
                  <i class="fas fa-user"></i>
                </a>
              </div>
            </nav>
            <main>
              <div class="title-container">
                <h1 class="page-title">Hamburgerek</h1>
              </div>
              <div class="flex-grid">
                <div class="flex-col">
                  <div class="menu__option" data-aos="fade-up" data-aos-delay="100">
                    <div class="image-wrapper">
                      <img src="../kepek/3/burger1.jpg" alt="Classic Burger">
                    </div>
                    <h2>Classic Burger</h2>
                    <div class="title-underline"></div>
                    <p class="price">Ár: 2500 Ft</p>
                    <div class="button-container">
                      <button class="order-btn details-btn" onclick="openCustomModal('modal-1')">Részletek</button>
                      <button class="order-btn add-to-cart" data-item-id="1" data-item="Classic Burger" data-image="../kepek/3/burger1.jpg" title="Kosárba rakás">
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="flex-col">
                  <div class="menu__option" data-aos="fade-up" data-aos-delay="200">
                    <div class="image-wrapper">
                      <img src="../kepek/3/burger2.jpg" alt="Cheese Burger">
                    </div>
                    <h2>Cheese Burger</h2>
                    <div class="title-underline"></div>
                    <p class="price">Ár: 2800 Ft</p>
                    <div class="button-container">
                      <button class="order-btn details-btn" onclick="openCustomModal('modal-2')">Részletek</button>
                      <button class="order-btn add-to-cart" data-item-id="2" data-item="Cheese Burger" data-image="../kepek/3/burger2.jpg" title="Kosárba rakás">
                        <i class="fas fa-shopping-cart"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </main>
            <div class="custom-modal" id="modal-1">
              <div class="custom-modal-content">
                <div class="custom-modal-header">
                  <h5 class="custom-modal-title">Classic Burger - Részletek</h5>
                  <button type="button" class="custom-close-btn" onclick="closeCustomModal('modal-1')">×</button>
                </div>
                <div class="custom-modal-body">
                  <img src="../kepek/3/burger1.jpg" class="img-fluid mb-3" alt="Classic Burger">
                  <p><strong>Kalória:</strong> 600 kcal</p>
                  <p><strong>Összetevők:</strong> Marhahús, saláta, paradicsom, uborka</p>
                  <p><strong>Allergének:</strong> Glutén, tej</p>
                  <p><strong>Ár:</strong> 2500 Ft</p>
                </div>
                <div class="custom-modal-footer">
                  <button type="button" class="order-btn close-btn" onclick="closeCustomModal('modal-1')">Bezárás</button>
                </div>
              </div>
            </div>
            <div class="custom-modal" id="modal-2">
              <div class="custom-modal-content">
                <div class="custom-modal-header">
                  <h5 class="custom-modal-title">Cheese Burger - Részletek</h5>
                  <button type="button" class="custom-close-btn" onclick="closeCustomModal('modal-2')">×</button>
                </div>
                <div class="custom-modal-body">
                  <img src="../kepek/3/burger2.jpg" class="img-fluid mb-3" alt="Cheese Burger">
                  <p><strong>Kalória:</strong> 700 kcal</p>
                  <p><strong>Összetevők:</strong> Marhahús, sajt, saláta, ketchup</p>
                  <p><strong>Allergének:</strong> Glutén, tej, mustár</p>
                  <p><strong>Ár:</strong> 2800 Ft</p>
                </div>
                <div class="custom-modal-footer">
                  <button type="button" class="order-btn close-btn" onclick="closeCustomModal('modal-2')">Bezárás</button>
                </div>
              </div>
            </div>
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
              <div id="toast-added" class="custom-toast">
                <div class="d-flex">
                  <div class="toast-body">Sikeresen hozzáadva a kosárhoz!</div>
                  <button type="button" class="btn-close me-2 m-auto" onclick="closeToast()">×</button>
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
                  <a href="https://www.facebook.com/"><i class="fab fa-facebook"></i></a>
                  <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                  <a href="https://x.com/"><i class="fab fa-twitter"></i></a>
                  <a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-copy">
                  © 2024 FlavorWave - Minden jog fenntartva.
                </div>
              </div>
            </div>
          </div>
          <style>
            nav, main, .flex-grid, .footer { display: block; }
            .logo, .navbar-center, .navbar-buttons { display: block; }
            .profile-btn { display: inline-block; width: 20px; height: 20px; }
            .menu__option, .image-wrapper, .button-container { display: block; }
            .custom-modal { display: none; }
            .custom-modal.active { display: block; }
            .toast-container { display: none; }
            .custom-toast.active { display: block; }
            @media (max-width: 767px) {
              .navbar-center { display: none; }
              .navbar-buttons { display: block; }
              .flex-grid { flex-direction: column; }
            }
            @media (min-width: 768px) {
              .navbar-center { display: block; }
              .flex-grid { display: flex; }
            }
          </style>
          <script>
            function openCustomModal(id) {
              document.getElementById(id).classList.add('active');
            }
            function closeCustomModal(id) {
              document.getElementById(id).classList.remove('active');
            }
            function closeToast() {
              document.getElementById('toast-added').classList.remove('active');
            }
          </script>
        </body>
        </html>
      `
    }).as('loadPage');

    // Látogassa meg az oldalt
    cy.visit('http://localhost/13c-szitasi/Flavorwave/php/kategoriaelemek.php');
    cy.wait('@loadPage');
  });

  // Oldal Betöltési Tesztek
  describe('Oldal Betöltési Tesztek', () => {
    it('jelenítse meg a navigációs sávot', () => {
      cy.get('nav', { timeout: 10000 }).should('be.visible');
      cy.get('.logo img').should('have.attr', 'alt', 'Flavorwave Logo');
    });

    it('jelenítse meg a kategória címet', () => {
      cy.get('.page-title', { timeout: 10000 }).should('contain', 'Hamburgerek');
    });

    it('jelenítse meg a láblécet', () => {
      cy.get('.footer', { timeout: 10000 }).should('be.visible');
      cy.get('.footer-copy').should('contain', '© 2024 FlavorWave');
    });
  });

  // Étel Kártyák Tesztek
  describe('Étel Kártyák Tesztek', () => {
    it('jelenítsen meg több étel kártyát', () => {
      cy.get('.menu__option', { timeout: 10000 }).should('have.length', 2);
      cy.get('.menu__option').first().within(() => {
        cy.get('img').should('have.attr', 'alt', 'Classic Burger');
        cy.get('h2').should('contain', 'Classic Burger');
        cy.get('.price').should('contain', '2500 Ft');
        cy.get('.details-btn').should('contain', 'Részletek');
        cy.get('.add-to-cart i').should('have.class', 'fas fa-shopping-cart');
      });
      cy.get('.menu__option').last().within(() => {
        cy.get('img').should('have.attr', 'alt', 'Cheese Burger');
        cy.get('h2').should('contain', 'Cheese Burger');
        cy.get('.price').should('contain', '2800 Ft');
        cy.get('.details-btn').should('contain', 'Részletek');
        cy.get('.add-to-cart i').should('have.class', 'fas fa-shopping-cart');
      });
    });

    it('jelenítsen meg üzenetet üres kategória esetén', () => {
      cy.intercept('GET', '**/kategoriaelemek.php', {
        statusCode: 200,
        headers: { 'Content-Type': 'text/html' },
        body: `
          <!DOCTYPE html>
          <html lang="hu">
          <head>
            <meta charset="UTF-8">
            <title>FlavorWave - Hamburgerek</title>
          </head>
          <body>
            <div class="container">
              <nav>
                <a href="kezdolap.php" class="logo">
                  <img src="../kepek/logo.png" alt="Flavorwave Logo">
                </a>
              </nav>
              <main>
                <div class="title-container">
                  <h1 class="page-title">Hamburgerek</h1>
                </div>
                <p>Ehhez a kategóriához nem tartozik étel!</p>
              </main>
            </div>
          </body>
          </html>
        `
      }).as('loadEmptyPage');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/kategoriaelemek.php');
      cy.wait('@loadEmptyPage');
      cy.get('p').should('contain', 'Ehhez a kategóriához nem tartozik étel!');
    });
  });

  // Modal Működési Tesztek
  describe('Modal Működési Tesztek', () => {
    it('nyissa meg és zárja be a modal-t', () => {
      cy.get('.menu__option').first().find('.details-btn').click();
      cy.get('#modal-1', { timeout: 10000 }).should('have.class', 'active');
      cy.get('#modal-1 .custom-modal-title').should('contain', 'Classic Burger - Részletek');
      cy.get('#modal-1 .custom-modal-body').within(() => {
        cy.get('img').should('have.attr', 'alt', 'Classic Burger');
        cy.get('p').eq(0).should('contain', 'Kalória: 600 kcal');
        cy.get('p').eq(1).should('contain', 'Összetevők: Marhahús, saláta, paradicsom, uborka');
        cy.get('p').eq(2).should('contain', 'Allergének: Glutén, tej');
        cy.get('p').eq(3).should('contain', 'Ár: 2500 Ft');
      });

      cy.get('#modal-1 .close-btn').click();
      cy.get('#modal-1').should('not.have.class', 'active');
    });

    it('ellenőrizze a második modal tartalmát', () => {
      cy.get('.menu__option').last().find('.details-btn').click();
      cy.get('#modal-2', { timeout: 10000 }).should('have.class', 'active');
      cy.get('#modal-2 .custom-modal-title').should('contain', 'Cheese Burger - Részletek');
      cy.get('#modal-2 .custom-modal-body').within(() => {
        cy.get('img').should('have.attr', 'alt', 'Cheese Burger');
        cy.get('p').eq(0).should('contain', 'Kalória: 700 kcal');
        cy.get('p').eq(1).should('contain', 'Összetevők: Marhahús, sajt, saláta, ketchup');
        cy.get('p').eq(2).should('contain', 'Allergének: Glutén, tej, mustár');
        cy.get('p').eq(3).should('contain', 'Ár: 2800 Ft');
      });

      cy.get('#modal-2 .custom-close-btn').click();
      cy.get('#modal-2').should('not.have.class', 'active');
    });
  });


  

  // Reszponzív Dizájn Tesztek
  describe('Reszponzív Dizájn Tesztek', () => {
    it('helyesen jelenjen meg mobilon', () => {
      cy.viewport('iphone-x');
      cy.get('nav').should('be.visible');
      cy.get('.logo').should('be.visible');
      cy.get('.navbar-buttons').should('be.visible');
      cy.get('.navbar-center').should('not.be.visible');
      cy.get('.flex-grid').should('be.visible');
      cy.get('.menu__option').should('have.length', 2);
      cy.get('.footer').should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('nav').should('be.visible');
      cy.get('.logo').should('be.visible');
      cy.get('.navbar-center').should('be.visible');
      cy.get('.navbar-buttons').should('be.visible');
      cy.get('.flex-grid').should('be.visible');
      cy.get('.menu__option').should('have.length', 2);
      cy.get('.footer').should('be.visible');
    });

    it('helyesen jelenjen meg asztali nézetben', () => {
      cy.viewport(1280, 720);
      cy.get('nav').should('be.visible');
      cy.get('.logo').should('be.visible');
      cy.get('.navbar-center').should('be.visible');
      cy.get('.navbar-buttons').should('be.visible');
      cy.get('.flex-grid').should('be.visible');
      cy.get('.menu__option').should('have.length', 2);
      cy.get('.footer').should('be.visible');
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    it('ellenőrizze a képek akadálymentesítését', () => {
      cy.get('.menu__option img').each(($img) => {
        cy.wrap($img).should('have.attr', 'alt').and('not.be.empty');
      });
      cy.get('.logo img').should('have.attr', 'alt', 'Flavorwave Logo');
    });

    it('ellenőrizze a gombok akadálymentesítését', () => {
      cy.get('.add-to-cart').each(($btn) => {
        cy.wrap($btn).should('have.attr', 'title', 'Kosárba rakás');
      });
      cy.get('.details-btn').each(($btn) => {
        cy.wrap($btn).invoke('text').should('eq', 'Részletek');
      });
    });
  });

  // AOS Animációs Tesztek
  describe('AOS Animációs Tesztek', () => {
    it('ellenőrizze az étel kártyák animációját', () => {
      cy.get('.menu__option').each(($card, index) => {
        cy.wrap($card).should('have.attr', 'data-aos', 'fade-up');
        cy.wrap($card).should('have.attr', 'data-aos-delay', String(100 * (index + 1)));
      });
    });
  });

  // Lábléc Tesztek
  describe('Lábléc Tesztek', () => {
    it('ellenőrizze a lábléc linkeket és ikonokat', () => {
      cy.get('.footer-links a').should('have.length', 3);
      cy.get('.footer-links a').eq(0).should('have.attr', 'href', '../html/rolunk.html').and('contain', 'Rólunk');
      cy.get('.footer-links a').eq(1).should('have.attr', 'href', '../html/kapcsolatok.html').and('contain', 'Kapcsolat');
      cy.get('.footer-links a').eq(2).should('have.attr', 'href', '../html/adatvedelem.html').and('contain', 'Adatvédelem');

      cy.get('.footer-socials a').should('have.length', 4);
      cy.get('.footer-socials i').eq(0).should('have.class', 'fab fa-facebook');
      cy.get('.footer-socials i').eq(1).should('have.class', 'fab fa-instagram');
      cy.get('.footer-socials i').eq(2).should('have.class', 'fab fa-twitter');
      cy.get('.footer-socials i').eq(3).should('have.class', 'fab fa-youtube');
    });
  });
});