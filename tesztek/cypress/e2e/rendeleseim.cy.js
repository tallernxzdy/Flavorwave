describe('FlavorWave Rendeléseim Oldal Tesztek', () => {
  beforeEach(() => {
    // Alapértelmezett várakozási idő növelése
    cy.visit('http://localhost/13c-szitasi/Flavorwave/php/rendeles_megtekintes.php', { timeout: 10000 }); // 10 másodperc
  });

  // Navigációs Tesztek
  describe('Navigációs Tesztek', () => {


    it('navigáljon a lábléc linkekre', () => {
      cy.get('.footer-links a', { timeout: 10000 }).contains('Rólunk').click();
      cy.url().should('include', 'rolunk.html');

      cy.go('back');
      cy.get('.footer-links a').contains('Kapcsolat').click();
      cy.url().should('include', 'kapcsolatok.html');

      cy.go('back');
      cy.get('.footer-links a').contains('Adatvédelem').click();
      cy.url().should('include', 'adatvedelem.html');
    });

    it('nyissa meg a közösségi média linkeket új lapon', () => {
      cy.get('.footer-socials a', { timeout: 10000 }).each(($el) => {
        cy.wrap($el).should('have.attr', 'target', '_blank');
      });
    });

    it('navigáljon vissza a főoldalra', () => {
      cy.get('.back-button', { timeout: 10000 }).contains('Vissza a főoldalra').click();
      cy.url().should('include', 'kezdolap.php');
    });
  });

  // Oldalcím Tesztek
  describe('Oldalcím Tesztek', () => {
    it('mutassa a helyes oldalcímet', () => {
      cy.get('#cim', { timeout: 10000 }).contains('Rendeléseim').should('be.visible');
    });

    it('ellenőrizze az oldal meta címét', () => {
      cy.title({ timeout: 10000 }).should('eq', 'Rendeléseim');
    });
  });

  // Bejelentkezési Állapot Tesztek
  describe('Bejelentkezési Állapot Tesztek', () => {
    it('mutassa a bejelentkezési üzenetet be nem jelentkezett felhasználóknak', () => {
      cy.get('.not-logged-in', { timeout: 10000 }).should('be.visible');
      cy.get('.not-logged-in p').contains('Nem vagy bejelentkezve! Kérlek, jelentkezz be a rendeléseid megtekintéséhez.').should('be.visible');
      cy.get('.not-logged-in a').contains('Bejelentkezés').should('be.visible').click();
      cy.url().should('include', 'bejelentkezes.php');
    });

    it('mutassa az üres rendelések üzenetet, ha nincs rendelés', () => {
      // Mockoljuk a bejelentkezett állapotot üres rendelésekkel
      cy.intercept('GET', '**/rendeles_megtekintes.php', (req) => {
        req.reply({
          statusCode: 200,
          body: `
            <div class="container">
              <h1 id="cim">Rendeléseim</h1>
              <p class="no-orders">Úgy tűnik, még nem adtál fel rendelést nálunk. Fedezd fel kínálatunkat, és rendeld meg kedvenceidet!</p>
              <a href="kezdolap.php" class="btn btn-secondary back-button">Vissza a főoldalra</a>
            </div>
          `
        });
      }).as('getNoOrders');
      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/rendeles_megtekintes.php');
      cy.get('.no-orders', { timeout: 10000 }).contains('Úgy tűnik, még nem adtál fel rendelést nálunk.').should('be.visible');
    });
  });

  // Rendelési Kártyák Tesztek
  describe('Rendelési Kártyák Tesztek', () => {
    beforeEach(() => {
      // Mockoljuk a bejelentkezett állapotot rendelésekkel
      cy.intercept('GET', '**/rendeles_megtekintes.php', (req) => {
        req.reply({
          statusCode: 200,
          body: `
            <div class="container">
              <h1 id="cim">Rendeléseim</h1>
              <div class="orders-grid">
                <div class="order-card">
                  <div class="order-header">
                    <h3>Rendelési azonosító: 123</h3>
                    <p class="order-date">Leadás dátuma: 2025-04-15 10:00:00</p>
                  </div>
                  <div class="order-details">
                    <p><strong>Állapot:</strong> Függőben</p>
                    <p><strong>Kézbesítés módja:</strong> Házhozszállítás</p>
                    <p><strong>Fizetési mód:</strong> Készpénz</p>
                    <p><strong>Megjegyzés:</strong> Kérem gyorsan!</p>
                    <p><strong>Rendelt tételek:</strong></p>
                    <ul>
                      <li>Margherita Pizza (2 db, 2 500 Ft)</li>
                      <li>Kóla (1 db, 500 Ft)</li>
                    </ul>
                  </div>
                </div>
                <div class="order-card">
                  <div class="order-header">
                    <h3>Rendelési azonosító: 124</h3>
                    <p class="order-date">Leadás dátuma: 2025-04-14 15:00:00</p>
                  </div>
                  <div class="order-details">
                    <p><strong>Állapot:</strong> Teljesítve</p>
                    <p><strong>Kézbesítés módja:</strong> Házhozszállítás</p>
                    <p><strong>Fizetési mód:</strong> Bankkártya</p>
                    <p><strong>Megjegyzés:</strong> Nincs megjegyzés</p>
                    <p><strong>Rendelt tételek:</strong></p>
                    <ul>
                      <li>Hamburger (1 db, 3 000 Ft)</li>
                    </ul>
                  </div>
                </div>
              </div>
              <a href="kezdolap.php" class="btn btn-secondary back-button">Vissza a főoldalra</a>
            </div>
          `
        });
      }).as('getOrders');
      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/rendeles_megtekintes.php', { timeout: 10000 });
    });

    it('mutassa a rendeléseket bejelentkezett felhasználóknak', () => {
      cy.get('.orders-grid', { timeout: 10000 }).should('be.visible');
      cy.get('.order-card', { timeout: 10000 }).should('have.length', 2);
    });

    it('ellenőrizze a rendelési adatokat', () => {
      cy.get('.order-card').eq(0).within(() => {
        cy.get('.order-header h3', { timeout: 10000 }).should('contain', 'Rendelési azonosító: 123');
        cy.get('.order-date').should('contain', 'Leadás dátuma: 2025-04-15 10:00:00');
        cy.get('.order-details p').eq(0).should('contain', 'Állapot: Függőben');
        cy.get('.order-details p').eq(1).should('contain', 'Kézbesítés módja: Házhozszállítás');
        cy.get('.order-details p').eq(2).should('contain', 'Fizetési mód: Készpénz');
        cy.get('.order-details p').eq(3).should('contain', 'Megjegyzés: Kérem gyorsan!');
        cy.get('.order-details ul li').should('have.length', 2);
        cy.get('.order-details ul li').eq(0).should('contain', 'Margherita Pizza (2 db, 2 500 Ft)');
        cy.get('.order-details ul li').eq(1).should('contain', 'Kóla (1 db, 500 Ft)');
      });

      cy.get('.order-card').eq(1).within(() => {
        cy.get('.order-header h3').should('contain', 'Rendelési azonosító: 124');
        cy.get('.order-date').should('contain', 'Leadás dátuma: 2025-04-14 15:00:00');
        cy.get('.order-details p').eq(0).should('contain', 'Állapot: Teljesítve');
        cy.get('.order-details p').eq(1).should('contain', 'Kézbesítés módja: Házhozszállítás');
        cy.get('.order-details p').eq(2).should('contain', 'Fizetési mód: Bankkártya');
        cy.get('.order-details p').eq(3).should('contain', 'Megjegyzés: Nincs megjegyzés');
        cy.get('.order-details ul li').should('have.length', 1);
        cy.get('.order-details ul li').eq(0).should('contain', 'Hamburger (1 db, 3 000 Ft)');
      });
    });

    it('kezelje az üres tételek esetét', () => {
      cy.intercept('GET', '**/rendeles_megtekintes.php', (req) => {
        req.reply({
          statusCode: 200,
          body: `
            <div class="container">
              <h1 id="cim">Rendeléseim</h1>
              <div class="orders-grid">
                <div class="order-card">
                  <div class="order-header">
                    <h3>Rendelési azonosító: 124</h3>
                    <p class="order-date">Leadás dátuma: 2025-04-15 12:00:00</p>
                  </div>
                  <div class="order-details">
                    <p><strong>Állapot:</strong> Függőben</p>
                    <p><strong>Kézbesítés módja:</strong> Házhozszállítás</p>
                    <p><strong>Fizetési mód:</strong> Készpénz</p>
                    <p><strong>Megjegyzés:</strong> Nincs megjegyzés</p>
                    <p><strong>Rendelt tételek:</strong></p>
                    <ul>
                      <li>Még nem találhatóak tételek ehhez a rendeléshez.</li>
                    </ul>
                  </div>
                </div>
              </div>
              <a href="kezdolap.php" class="btn btn-secondary back-button">Vissza a főoldalra</a>
            </div>
          `
        });
      }).as('getEmptyOrder');
      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/rendeles_megtekintes.php', { timeout: 10000 });
      cy.get('.order-card .order-details ul li', { timeout: 10000 }).should('contain', 'Még nem találhatóak tételek ehhez a rendeléshez.');
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    it('rendelkezzen megfelelő alt attribútumokkal a képekhez', () => {
      cy.get('img', { timeout: 10000 }).each(($img) => {
        cy.wrap($img).should('have.attr', 'alt').and('not.be.empty');
      });
    });

    it('ellenőrizze a gombok akadálymentes nevét', () => {
      cy.get('.back-button', { timeout: 10000 }).should('contain', 'Vissza a főoldalra');
      cy.get('.not-logged-in a', { timeout: 10000 }).should('contain', 'Bejelentkezés');
    });
  });

  // Reszponzív Dizájn Tesztek
  describe('Reszponzív Dizájn Tesztek', () => {
    it('helyesen jelenjen meg mobilon', () => {
      cy.viewport('iphone-x');
      cy.get('#cim', { timeout: 10000 }).should('be.visible');
      cy.get('.container', { timeout: 10000 }).should('be.visible');
      cy.get('.back-button', { timeout: 10000 }).should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('.not-logged-in', { timeout: 10000 }).should('be.visible');
      cy.get('.footer', { timeout: 10000 }).should('be.visible');
    });
  });

  // Betöltési Tesztek
  describe('Betöltési Tesztek', () => {
    it('ellenőrizze a külső erőforrások betöltését', () => {
      cy.get('link[href*="bootstrap"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="font-awesome"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="googleapis"]', { timeout: 10000 }).should('exist');
      cy.get('script[src*="bootstrap"]', { timeout: 10000 }).should('exist');
    });

    it('ellenőrizze a helyi CSS fájlok betöltését', () => {
      cy.get('link[href*="../css/fooldal/ujfooldal.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/rendeles_megtekint.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/footer.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/navbar.css"]', { timeout: 10000 }).should('exist');
    });
  });
});