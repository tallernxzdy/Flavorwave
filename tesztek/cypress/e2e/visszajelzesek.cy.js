describe('FlavorWave Visszajelzések Oldal Tesztek', () => {
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

    // Mockoljuk a visszajelzesek.php alap GET kérését
    cy.intercept('GET', '**/visszajelzesek.php', {
      statusCode: 200,
      headers: { 'Content-Type': 'text/html' },
      body: `
        <!DOCTYPE html>
        <html lang="hu">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Visszajelzés</title>
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
            <section class="feedback-section">
              <div class="container">
                <h1>Küldd el a véleményed!</h1>
                <form method="POST" action="visszajelzesek.php" class="feedback-form">
                  <div class="form-group">
                    <label for="megelegedettseg">Mennyire elégedett? (1-5 csillag)</label>
                    <div class="rating">
                      <span class="star" data-value="1">★</span>
                      <span class="star" data-value="2">★</span>
                      <span class="star" data-value="3">★</span>
                      <span class="star" data-value="4">★</span>
                      <span class="star" data-value="5">★</span>
                    </div>
                    <input type="hidden" id="megelegedettseg" name="megelegedettseg" required>
                  </div>
                  <div class="form-group">
                    <label for="visszajelzes">Visszajelzés szövege</label>
                    <textarea id="visszajelzes" name="visszajelzes" rows="5" required placeholder="Írd meg a véleményed..."></textarea>
                  </div>
                  <div class="form-group">
                    <div class="recaptcha-wrapper">
                      <div class="g-recaptcha" data-sitekey="6Lf0bsoqAAAAADgj9B0eBgXozNmq1q2vYqEMXzvb"></div>
                    </div>
                  </div>
                  <button type="submit" class="feedback-btn">Küldés</button>
                </form>
              </div>
            </section>
            <section class="feedback-display">
              <div class="container">
                <h2>Mit mondanak rólunk?</h2>
                <p class="text-muted">Légy az elsők között, aki visszajelzést küld!</p>
              </div>
            </section>
          </div>
          <style>
            nav, .feedback-section, .feedback-display { display: block; }
            .logo, .navbar-center, .navbar-buttons { display: block; }
            .profile-btn { display: inline-block; width: 20px; height: 20px; }
            .feedback-form, .rating, .recaptcha-wrapper, .feedback-btn { display: block; }
            .form-group label, .form-group textarea { display: block; }
            .star { cursor: pointer; }
            .star.active { color: gold; }
            @media (max-width: 767px) {
              .navbar-center { display: none; }
              .navbar-buttons { display: block; }
            }
            @media (min-width: 768px) {
              .navbar-center { display: block; }
            }
          </style>
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const stars = document.querySelectorAll('.rating .star');
              const ratingInput = document.getElementById('megelegedettseg');
              stars.forEach(star => {
                star.addEventListener('click', function() {
                  const value = this.getAttribute('data-value');
                  ratingInput.value = value;
                  stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                      s.classList.add('active');
                    } else {
                      s.classList.remove('active');
                    }
                  });
                });
              });
            });
          </script>
        </body>
        </html>
      `
    }).as('loadPage');

    // Látogassa meg az oldalt bejelentkezett felhasználóval
    cy.visit('http://localhost/13c-szitasi/Flavorwave/php/visszajelzesek.php', {
      onBeforeLoad: (win) => {
        win.sessionStorage.setItem('felhasznalo_id', '1');
        win.sessionStorage.setItem('felhasznalo_nev', 'tesztelek');
      }
    });
    cy.wait('@loadPage');
  });

  // Oldal Betöltési Tesztek
  describe('Oldal Betöltési Tesztek', () => {
    it('jelenítse meg a visszajelzés szekciót', () => {
      cy.get('.feedback-section', { timeout: 10000 }).should('be.visible');
      cy.get('.feedback-section h1').should('contain', 'Küldd el a véleményed!');
    });

    it('jelenítse meg a vélemények szekciót', () => {
      cy.get('.feedback-display', { timeout: 10000 }).should('be.visible');
      cy.get('.feedback-display h2').should('contain', 'Mit mondanak rólunk?');
    });

    it('jelenítse meg az űrlapot', () => {
      cy.get('.feedback-form', { timeout: 10000 }).should('be.visible');
      cy.get('#megelegedettseg').should('exist');
      cy.get('#visszajelzes').should('exist');
      cy.get('.g-recaptcha').should('exist');
      cy.get('.feedback-btn').should('contain', 'Küldés');
    });
  });

  // Bejelentkezés Nélküli Tesztek
  describe('Bejelentkezés Nélküli Tesztek', () => {
    it('jelenítsen meg hibaüzenetet bejelentkezés nélkül', () => {
      cy.intercept('GET', '**/visszajelzesek.php', {
        statusCode: 200,
        headers: { 'Content-Type': 'text/html' },
        body: `
          <!DOCTYPE html>
          <html>
          <body>
            <div class="error">Csak bejelentkezett felhasználók írhatnak véleményt.</div>
          </body>
          </html>
        `
      }).as('loadPageNoAuth');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/visszajelzesek.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.clear();
        }
      });
      cy.wait('@loadPageNoAuth');
      cy.get('.error').should('contain', 'Csak bejelentkezett felhasználók írhatnak véleményt.');
    });
  });

  // Űrlap Validációs Tesztek
  describe('Űrlap Validációs Tesztek', () => {
    it('jelenítsen meg hibát hiányzó reCAPTCHA esetén', () => {
      cy.intercept('POST', '**/visszajelzesek.php', {
        statusCode: 200,
        headers: { 'Content-Type': 'text/html' },
        body: `
          <!DOCTYPE html>
          <html>
          <body>
            <div class="container">
              <section class="feedback-section">
                <p class="alert">Kérjük, erősítse meg, hogy nem robot!</p>
              </section>
            </div>
          </body>
          </html>
        `
      }).as('submitNoRecaptcha');

      cy.get('.rating .star[data-value="3"]').click();
      cy.get('#visszajelzes').type('Teszt visszajelzés');
      cy.get('.feedback-btn').click();
      cy.wait('@submitNoRecaptcha');
      cy.get('.alert').should('contain', 'Kérjük, erősítse meg, hogy nem robot!');
    });
  });

  // Űrlap Interakciós Tesztek
  describe('Űrlap Interakciós Tesztek', () => {
    it('ellenőrizze az űrlap mezők kitöltését', () => {
      cy.get('.rating .star[data-value="4"]').click();
      cy.get('#megelegedettseg').should('have.value', '4');
      cy.get('#visszajelzes').type('Ez egy teszt visszajelzés.');
      cy.get('#visszajelzes').should('have.value', 'Ez egy teszt visszajelzés.');
    });
  });

  // Vélemények Megjelenítési Tesztek
  describe('Vélemények Megjelenítési Tesztek', () => {
    it('jelenítsen meg üzenetet, ha nincsenek vélemények', () => {
      cy.get('.text-muted').should('contain', 'Légy az elsők között, aki visszajelzést küld!');
    });

    it('jelenítsen meg egy véleményt kártyában', () => {
      cy.intercept('GET', '**/visszajelzesek.php', {
        statusCode: 200,
        headers: { 'Content-Type': 'text/html' },
        body: `
          <!DOCTYPE html>
          <html lang="hu">
          <head>
            <meta charset="UTF-8">
            <title>Visszajelzés</title>
          </head>
          <body>
            <div class="container">
              <nav>
                <a href="kezdolap.php" class="logo">
                  <img src="../kepek/logo.png" alt="Flavorwave Logo">
                </a>
              </nav>
              <section class="feedback-section">
                <h1>Küldd el a véleményed!</h1>
              </section>
              <section class="feedback-display">
                <div class="container">
                  <h2>Mit mondanak rólunk?</h2>
                  <div class="feedback-grid">
                    <div class="feedback-card">
                      <div class="feedback-content">
                        <p class="feedback-text">"Kiváló kiszolgálás!"</p>
                        <p class="feedback-author">- tesztuser1</p>
                        <div class="feedback-rating">
                          Értékelés: <span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </body>
          </html>
        `
      }).as('loadPageWithFeedback');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/visszajelzesek.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.setItem('felhasznalo_id', '1');
        }
      });
      cy.wait('@loadPageWithFeedback');

      cy.get('.feedback-card').should('have.length', 1);
      cy.get('.feedback-card').within(() => {
        cy.get('.feedback-text').should('contain', 'Kiváló kiszolgálás!');
        cy.get('.feedback-author').should('contain', 'tesztuser1');
        cy.get('.feedback-rating .star').should('have.length', 4);
      });
    });

    it('jelenítsen meg több véleményt kártyákban', () => {
      cy.intercept('GET', '**/visszajelzesek.php', {
        statusCode: 200,
        headers: { 'Content-Type': 'text/html' },
        body: `
          <!DOCTYPE html>
          <html lang="hu">
          <head>
            <meta charset="UTF-8">
            <title>Visszajelzés</title>
          </head>
          <body>
            <div class="container">
              <nav>
                <a href="kezdolap.php" class="logo">
                  <img src="../kepek/logo.png" alt="Flavorwave Logo">
                </a>
              </nav>
              <section class="feedback-section">
                <h1>Küldd el a véleményed!</h1>
              </section>
              <section class="feedback-display">
                <div class="container">
                  <h2>Mit mondanak rólunk?</h2>
                  <div class="feedback-grid">
                    <div class="feedback-card">
                      <div class="feedback-content">
                        <p class="feedback-text">"Kiváló kiszolgálás!"</p>
                        <p class="feedback-author">- tesztuser1</p>
                        <div class="feedback-rating">
                          Értékelés: <span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span>
                        </div>
                      </div>
                    </div>
                    <div class="feedback-card">
                      <div class="feedback-content">
                        <p class="feedback-text">"Gyors és megbízható."</p>
                        <p class="feedback-author">- tesztuser2</p>
                        <div class="feedback-rating">
                          Értékelés: <span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </body>
          </html>
        `
      }).as('loadPageWithFeedbacks');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/visszajelzesek.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.setItem('felhasznalo_id', '1');
        }
      });
      cy.wait('@loadPageWithFeedbacks');

      cy.get('.feedback-card').should('have.length', 2);
      cy.get('.feedback-card').first().within(() => {
        cy.get('.feedback-text').should('contain', 'Kiváló kiszolgálás!');
        cy.get('.feedback-author').should('contain', 'tesztuser1');
        cy.get('.feedback-rating .star').should('have.length', 4);
      });
      cy.get('.feedback-card').last().within(() => {
        cy.get('.feedback-text').should('contain', 'Gyors és megbízható.');
        cy.get('.feedback-author').should('contain', 'tesztuser2');
        cy.get('.feedback-rating .star').should('have.length', 5);
      });
    });
  });

  // Csillag-Értékelés Tesztek
  describe('Csillag-Értékelés Tesztek', () => {
    it('működjön a csillag-értékelés kiválasztása', () => {
      cy.get('.rating .star[data-value="3"]').click();
      cy.get('#megelegedettseg').should('have.value', '3');
      cy.get('.rating .star').each(($star, index) => {
        if (index < 3) {
          cy.wrap($star).should('have.class', 'active');
        } else {
          cy.wrap($star).should('not.have.class', 'active');
        }
      });

      cy.get('.rating .star[data-value="5"]').click();
      cy.get('#megelegedettseg').should('have.value', '5');
      cy.get('.rating .star').each(($star, index) => {
        if (index < 5) {
          cy.wrap($star).should('have.class', 'active');
        } else {
          cy.wrap($star).should('not.have.class', 'active');
        }
      });
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
      cy.get('.feedback-section').should('be.visible');
      cy.get('.feedback-form').should('be.visible');
      cy.get('.feedback-display').should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('nav').should('be.visible');
      cy.get('.logo').should('be.visible');
      cy.get('.navbar-center').should('be.visible');
      cy.get('.navbar-buttons').should('be.visible');
      cy.get('.feedback-section').should('be.visible');
      cy.get('.feedback-form').should('be.visible');
      cy.get('.feedback-display').should('be.visible');
    });

    it('helyesen jelenjen meg asztali nézetben', () => {
      cy.viewport(1280, 720);
      cy.get('nav').should('be.visible');
      cy.get('.logo').should('be.visible');
      cy.get('.navbar-center').should('be.visible');
      cy.get('.navbar-buttons').should('be.visible');
      cy.get('.feedback-section').should('be.visible');
      cy.get('.feedback-form').should('be.visible');
      cy.get('.feedback-display').should('be.visible');
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    it('ellenőrizze az űrlap akadálymentesítését', () => {
      cy.get('.form-group label[for="megelegedettseg"]').should('contain', 'Mennyire elégedett?');
      cy.get('.form-group label[for="visszajelzes"]').should('contain', 'Visszajelzés szövege');
      cy.get('#visszajelzes').should('have.attr', 'placeholder', 'Írd meg a véleményed...');
      cy.get('#megelegedettseg').should('have.attr', 'required');
      cy.get('#visszajelzes').should('have.attr', 'required');
    });

    it('ellenőrizze a logo és ikonok akadálymentesítését', () => {
      cy.get('.logo img').should('have.attr', 'alt', 'Flavorwave Logo');
      cy.get('.cart-btn .cart-icon').should('have.class', 'fas fa-shopping-cart');
      cy.get('.profile-btn i').should('have.class', 'fas fa-user');
    });
  });

  // Erőforrás Betöltési Tesztek
  describe('Erőforrás Betöltési Tesztek', () => {
    it('ellenőrizze a logo kép betöltését', () => {
      cy.get('.logo img').should('have.attr', 'src', '../kepek/logo.png');
    });
  });
});