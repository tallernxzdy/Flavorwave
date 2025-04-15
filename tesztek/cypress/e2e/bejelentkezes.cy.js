describe('FlavorWave Bejelentkezés Oldal Tesztek', () => {
  beforeEach(() => {
    // Látogassa meg a bejelentkezés oldalt minden teszt előtt, növelt időzítéssel
    cy.visit('http://localhost/13c-szitasi/Flavorwave/php/bejelentkezes.php', { timeout: 10000 });
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

    it('navigáljon a regisztrációs oldalra', () => {
      cy.get('.container p a', { timeout: 10000 }).contains('Regisztrálj most!').click();
      cy.url().should('include', 'regisztracio.php');
    });
  });

  // Oldalcím Tesztek
  describe('Oldalcím Tesztek', () => {
    it('mutassa a helyes oldalcímet', () => {
      cy.get('.container h2', { timeout: 10000 }).contains('Bejelentkezés').should('be.visible');
    });

    it('ellenőrizze az oldal meta címét', () => {
      cy.title({ timeout: 10000 }).should('eq', 'FlavorWave | Bejelentkezés');
    });
  });

  // Űrlap Tesztek
  describe('Űrlap Tesztek', () => {
    it('ellenőrizze az űrlap elemeit', () => {
      cy.get('#loginForm', { timeout: 10000 }).should('be.visible');
      cy.get('input[name="username"]').should('be.visible').and('have.attr', 'required');
      cy.get('input[name="password"]').should('be.visible').and('have.attr', 'required');
      cy.get('.g-recaptcha').should('be.visible');
      cy.get('button[type="submit"]').contains('Bejelentkezés').should('be.visible');
    });

    it('ne küldje el az űrlapot üres mezőkkel', () => {
      cy.get('button[type="submit"]').click();
      // Ellenőrizzük a HTML5 validációt a :invalid pszeudo-osztály helyett
      cy.get('#username').invoke('prop', 'validity').its('valueMissing').should('be.true');
      cy.get('#password').invoke('prop', 'validity').its('valueMissing').should('be.true');
      cy.url().should('include', 'bejelentkezes.php'); // Nem történik átirányítás
    });

    it('jelenítse meg a hibát helytelen felhasználónév/jelszó esetén', () => {
      // Mockoljuk a szerver választ hibával
      cy.intercept('POST', '**/bejelentkezes.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Bejelentkezés</h2>
            <form id="loginForm" method="POST">
              <label for="username">Felhasználónév:</label>
              <input type="text" name="username" id="username" required>
              <label for="password">Jelszó:</label>
              <div class="input-container">
                <input type="password" name="password" id="password" required>
                <span class="input_img" data-role="toggle" id="togglePassword"></span>
              </div>
              <div class="g-recaptcha"></div><br>
              <div id="errorContainer" style="color: red; margin-top: 10px;">
                <p class="error">Hibás jelszó vagy felhasználónév!</p>
              </div>
              <button type="submit">Bejelentkezés</button>
            </form>
            <p>Nincs fiókod? <a href="regisztracio.php">Regisztrálj most!</a></p>
          </div>
        `
      }).as('loginError');

      cy.get('input[name="username"]').type('rosszfelhasznalo');
      cy.get('input[name="password"]').type('rosszjelszo');
      // Mockoljuk a reCAPTCHA-t
      cy.window().then((win) => {
        win.document.querySelector('.g-recaptcha').setAttribute('data-response', 'mocked-recaptcha-response');
      });
      cy.get('button[type="submit"]').click();
      cy.get('#errorContainer p.error', { timeout: 10000 }).should('contain', 'Hibás jelszó vagy felhasználónév!');
    });

    it('jelenítse meg a reCAPTCHA hibát, ha a reCAPTCHA nincs kitöltve', () => {
      // Mockoljuk a szerver választ reCAPTCHA hibával
      cy.intercept('POST', '**/bejelentkezes.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Bejelentkezés</h2>
            <form id="loginForm" method="POST">
              <label for="username">Felhasználónév:</label>
              <input type="text" name="username" id="username" required>
              <label for="password">Jelszó:</label>
              <div class="input-container">
                <input type="password" name="password" id="password" required>
                <span class="input_img" data-role="toggle" id="togglePassword"></span>
              </div>
              <div class="g-recaptcha"></div><br>
              <div id="errorContainer" style="color: red; margin-top: 10px;">
                <p class="error">reCAPTCHA ellenőrzés sikertelen, próbáld újra!</p>
              </div>
              <button type="submit">Bejelentkezés</button>
            </form>
            <p>Nincs fiókod? <a href="regisztracio.php">Regisztrálj most!</a></p>
          </div>
        `
      }).as('recaptchaError');

      cy.get('input[name="username"]').type('tesztfelhasznalo');
      cy.get('input[name="password"]').type('tesztjelszo');
      cy.get('button[type="submit"]').click();
      cy.get('#errorContainer p.error', { timeout: 10000 }).should('contain', 'reCAPTCHA ellenőrzés sikertelen, próbáld újra!');
    });

    it('sikeres bejelentkezés átirányít a főoldalra', () => {
      // Mockoljuk a sikeres bejelentkezést
      cy.intercept('POST', '**/bejelentkezes.php', {
        statusCode: 302,
        headers: { Location: 'kezdolap.php' }
      }).as('loginSuccess');

      cy.get('input[name="username"]').type('tesztfelhasznalo');
      cy.get('input[name="password"]').type('tesztjelszo');
      // Mockoljuk a reCAPTCHA-t
      cy.window().then((win) => {
        win.document.querySelector('.g-recaptcha').setAttribute('data-response', 'mocked-recaptcha-response');
      });
      cy.get('button[type="submit"]').click();
      cy.url({ timeout: 10000 }).should('include', 'kezdolap.php');
    });
  });

  // Jelszó Láthatóság Tesztek
  describe('Jelszó Láthatóság Tesztek', () => {
    it('váltson a jelszó láthatósága', () => {
      cy.get('#password', { timeout: 10000 }).should('have.attr', 'type', 'password');
      cy.get('#togglePassword', { timeout: 10000 }).click();
      cy.get('#password').should('have.attr', 'type', 'text');
      cy.get('#togglePassword').click();
      cy.get('#password').should('have.attr', 'type', 'password');
    });

    it('ellenőrizze a jelszó ikon váltását', () => {
      cy.get('#togglePassword svg path', { timeout: 10000 }).invoke('attr', 'd').should('include', 'M13.359 11.238');
      cy.get('#togglePassword').click();
      cy.get('#togglePassword svg path', { timeout: 10000 }).invoke('attr', 'd').should('include', 'M16 8s-3-5.5-8-5.5');
      cy.get('#togglePassword').click();
      cy.get('#togglePassword svg path', { timeout: 10000 }).invoke('attr', 'd').should('include', 'M13.359 11.238');
    });
  });

  // Kosár Egyesítés Tesztek
  describe('Kosár Egyesítés Tesztek', () => {
    it('sikeres bejelentkezés után tisztítsa a vendég kosarat', () => {
      // Mockoljuk a sütit és a sikeres bejelentkezést
      cy.setCookie('guest_cart', JSON.stringify({ 1: 2, 2: 1 }));
      cy.intercept('POST', '**/bejelentkezes.php', (req) => {
        req.reply({
          statusCode: 302,
          headers: { 
            Location: 'kezdolap.php',
            'Set-Cookie': 'guest_cart=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/'
          }
        });
      }).as('loginSuccess');

      cy.get('input[name="username"]').type('tesztfelhasznalo');
      cy.get('input[name="password"]').type('tesztjelszo');
      cy.window().then((win) => {
        win.document.querySelector('.g-recaptcha').setAttribute('data-response', 'mocked-recaptcha-response');
      });
      cy.get('button[type="submit"]').click();
      cy.url({ timeout: 10000 }).should('include', 'kezdolap.php');
      cy.getCookie('guest_cart').should('be.null');
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    it('rendelkezzen megfelelő label attribútumokkal', () => {
      cy.get('label[for="username"]', { timeout: 10000 }).should('contain', 'Felhasználónév:');
      cy.get('label[for="password"]', { timeout: 10000 }).should('contain', 'Jelszó:');
    });

    it('ellenőrizze az űrlap akadálymentes nevét', () => {
      cy.get('input[name="username"]', { timeout: 10000 }).should('have.attr', 'id', 'username');
      cy.get('input[name="password"]', { timeout: 10000 }).should('have.attr', 'id', 'password');
      cy.get('button[type="submit"]', { timeout: 10000 }).should('contain', 'Bejelentkezés');
    });

    it('rendelkezzen megfelelő alt attribútumokkal a képekhez', () => {
      cy.get('img', { timeout: 10000 }).each(($img) => {
        cy.wrap($img).should('have.attr', 'alt').and('not.be.empty');
      });
    });
  });

  // Reszponzív Dizájn Tesztek
  describe('Reszponzív Dizájn Tesztek', () => {
    it('helyesen jelenjen meg mobilon', () => {
      cy.viewport('iphone-x');
      cy.get('.container h2', { timeout: 10000 }).should('be.visible');
      cy.get('#loginForm', { timeout: 10000 }).should('be.visible');
      cy.get('.footer', { timeout: 10000 }).should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('input[name="username"]', { timeout: 10000 }).should('be.visible');
      cy.get('input[name="password"]', { timeout: 10000 }).should('be.visible');
      cy.get('button[type="submit"]', { timeout: 10000 }).should('be.visible');
    });
  });

  // Betöltési Tesztek
  describe('Betöltési Tesztek', () => {
    it('ellenőrizze a külső erőforrások betöltését', () => {
      cy.get('script[src*="google.com/recaptcha"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="bootstrap"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="font-awesome"]', { timeout: 10000 }).should('exist');
      cy.get('script[src*="bootstrap"]', { timeout: 10000 }).should('exist');
    });

    it('ellenőrizze a helyi CSS fájlok betöltését', () => {
      cy.get('link[href*="../css/bejelentkezes.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/navbar.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/footer.css"]', { timeout: 10000 }).should('exist');
    });
  });
});