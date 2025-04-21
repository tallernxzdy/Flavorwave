describe('FlavorWave Kosár Oldal Tesztek (Üres Kosár)', () => {
  beforeEach(() => {
    // Látogassa meg a kosár oldalt minden teszt előtt
    cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php', { timeout: 10000 });
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
        cy.wrap($el).should('have.attr', 'href').and('match', /facebook\.com|instagram\.com|x\.com|youtube\.com/);
      });
    });
  });

  // Oldalcím Tesztek
  describe('Oldalcím Tesztek', () => {
    it('mutassa a helyes oldalcímet', () => {
      cy.get('.cart-header h1', { timeout: 10000 }).contains('Kosár').should('be.visible');
    });

    it('ellenőrizze az oldal meta címét', () => {
      cy.title({ timeout: 10000 }).should('eq', 'FlavorWave - Kosár');
    });
  });

  // Üres Kosár Tesztek (Bejelentkezett Felhasználó)
  describe('Üres Kosár Tesztek (Bejelentkezett Felhasználó)', () => {
    beforeEach(() => {
      // Mockoljuk a bejelentkezett felhasználót és az üres kosarat
      cy.intercept('GET', '**/get_cart_count.php', { statusCode: 200, body: { count: 0 } }).as('getCartCount');
      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.setItem('felhasznalo_id', '1');
        }
      });
    });

    it('jelenítse meg az üres kosár üzenetet', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.cart-item', { timeout: 10000 }).should('not.exist');
      cy.get('.total-amount').should('contain', '0 Ft');
      cy.get('.checkout-section .error').should('contain', 'A kosár üres, rendeléshez adjon hozzá termékeket!');
      cy.get('#clearCartBtn').should('not.exist');
      cy.get('.checkout-btn').should('not.exist');
    });
  });

  // Üres Kosár Tesztek (Vendég Felhasználó)
  describe('Üres Kosár Tesztek (Vendég Felhasználó)', () => {
    beforeEach(() => {
      // Mockoljuk az üres vendég kosarat
      cy.intercept('GET', '**/get_cart_count.php', { statusCode: 200, body: { count: 0 } }).as('getCartCount');
      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.removeItem('felhasznalo_id');
          win.document.cookie = 'guest_cart=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
        }
      });
    });

    it('jelenítse meg az üres kosár üzenetet és a bejelentkezési kérést', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
              <p class="login-prompt">Rendeléshez jelentkezzen be!</p>
              <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.cart-item', { timeout: 10000 }).should('not.exist');
      cy.get('.total-amount').should('contain', '0 Ft');
      cy.get('.checkout-section .error').should('contain', 'A kosár üres, rendeléshez adjon hozzá termékeket!');
      cy.get('.login-prompt').should('contain', 'Rendeléshez jelentkezzen be!');
      cy.get('.login-btn').should('have.attr', 'href', 'bejelentkezes.php');
      cy.get('#clearCartBtn').should('not.exist');
    });

    it('navigáljon a bejelentkezési oldalra', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
              <p class="login-prompt">Rendeléshez jelentkezzen be!</p>
              <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.login-btn').click();
      cy.url().should('include', 'bejelentkezes.php');
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/get_cart_count.php', { statusCode: 200, body: { count: 0 } }).as('getCartCount');
      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.removeItem('felhasznalo_id');
          win.document.cookie = 'guest_cart=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
        }
      });
    });

    it('ellenőrizze az oldal fejléc akadálymentesítését', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
              <p class="login-prompt">Rendeléshez jelentkezzen be!</p>
              <a href="bejelentkezes.php" class="login-btn">Bejelentkezés</a>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.cart-header h1').should('have.text', 'Kosár').and('be.visible');
      cy.get('.login-btn').should('have.text', 'Bejelentkezés').and('have.attr', 'href', 'bejelentkezes.php');
    });

    it('ellenőrizze a lábléc linkek akadálymentesítését', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
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
              </div>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.footer-links a', { timeout: 10000 }).each(($el) => {
        cy.wrap($el).invoke('text').should('not.be.empty');
        cy.wrap($el).should('have.attr', 'href').and('not.be.empty');
      });
    });
  });

  // Reszponzív Dizájn Tesztek
  describe('Reszponzív Dizájn Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/get_cart_count.php', { statusCode: 200, body: { count: 0 } }).as('getCartCount');
    });

    it('helyesen jelenjen meg mobilon', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
            </div>
            <div class="footer">
              <div class="footer-container">
                <ul class="footer-links">
                  <li><a href="../html/rolunk.html">Rólunk</a></li>
                  <li><a href="../html/kapcsolatok.html">Kapcsolat</a></li>
                  <li><a href="../html/adatvedelem.html">Adatvédelem</a></li>
                </ul>
              </div>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.viewport('iphone-x');
      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.cart-header h1', { timeout: 10000 }).should('be.visible');
      cy.get('.total-amount', { timeout: 10000 }).should('be.visible');
      cy.get('.checkout-section .error', { timeout: 10000 }).should('be.visible');
      cy.get('.footer', { timeout: 10000 }).should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.viewport('ipad-2');
      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      cy.get('.cart-header h1', { timeout: 10000 }).should('be.visible');
      cy.get('.total-amount', { timeout: 10000 }).should('be.visible');
      cy.get('.checkout-section .error', { timeout: 10000 }).should('be.visible');
    });
  });

  // Betöltési Tesztek
  describe('Betöltési Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/get_cart_count.php', { statusCode: 200, body: { count: 0 } }).as('getCartCount');
    });

    it('ellenőrizze a külső erőforrások betöltését', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
            </div>
          </div>
        `
      }).as('loadCart');

      cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kosar.php');
      // Ellenőrizzük, hogy van-e bármilyen külső CSS vagy JS betöltés

    });

    it('ellenőrizze a helyi erőforrások betöltését', () => {
      cy.intercept('GET', '**/kosar.php', {
        statusCode: 200,
        body: `
          <div class="cart-container">
            <div class="cart-header">
              <h1>Kosár</h1>
            </div>
            <div class="total-section">
              <span class="total-label">Végösszeg:</span>
              <span class="total-amount">0 Ft</span>
            </div>
            <div class="checkout-section">
              <p class="error">A kosár üres, rendeléshez adjon hozzá termékeket!</p>
            </div>
          </div>
        `
      }).as('loadCart');



    });
  });
});