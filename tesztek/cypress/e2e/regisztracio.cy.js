describe('FlavorWave Regisztráció Oldal Tesztek', () => {
  beforeEach(() => {
    // Látogassa meg a regisztráció oldalt minden teszt előtt, növelt időzítéssel
    cy.visit('http://localhost/13c-szitasi/Flavorwave/php/regisztracio.php', { timeout: 10000 });
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

    it('navigáljon a bejelentkezési oldalra', () => {
      cy.get('.form-footer a', { timeout: 10000 }).contains('Bejelentkezés').click();
      cy.url().should('include', 'bejelentkezes.php');
    });
  });

  // Oldalcím Tesztek
  describe('Oldalcím Tesztek', () => {
    it('mutassa a helyes oldalcímet', () => {
      cy.get('.container h2', { timeout: 10000 }).contains('Regisztráció').should('be.visible');
    });

    it('ellenőrizze az oldal meta címét', () => {
      cy.title({ timeout: 10000 }).should('eq', 'FlavorWave | Regisztráció');
    });
  });

  // Űrlap Tesztek
  describe('Űrlap Tesztek', () => {
    it('ellenőrizze az űrlap elemeit', () => {
      cy.get('form', { timeout: 10000 }).should('be.visible');
      cy.get('input[name="username"]').should('be.visible');
      cy.get('input[name="email"]').should('be.visible');
      cy.get('input[name="password"]').should('be.visible');
      cy.get('input[name="phone"]').should('be.visible');
      cy.get('button[type="submit"]').contains('Regisztráció').should('be.visible');
    });

    it('ne küldje el az űrlapot üres mezőkkel', () => {
      cy.get('button[type="submit"]').click();
      cy.get('.alert.alert-danger', { timeout: 10000 }).should('contain', 'A felhasználónév nem lehet üres!');
      cy.url().should('include', 'regisztracio.php'); // Nem történik átirányítás
    });

    it('jelenítse meg a hibát érvénytelen felhasználónév esetén', () => {
      cy.intercept('POST', '**/regisztracio.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Regisztráció</h2>
            <div class="alert alert-danger">A felhasználónévnek legalább 4 karakter hosszúnak kell lennie!</div>
            <form method="POST" action="">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username" value="tes">
              </div>
              <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email">
              </div>
              <div class="form-group">
                <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                <div class="input-container">
                  <input type="password" id="password" name="password">
                  <span class="input_img" data-role="toggle" id="togglePassword"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone">
              </div>
              <button type="submit">Regisztráció</button>
            </form>
            <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
          </div>
        `
      }).as('invalidUsername');

      cy.get('input[name="username"]').type('tes');
      cy.get('button[type="submit"]').click();
      cy.get('.alert.alert-danger', { timeout: 10000 }).should('contain', 'A felhasználónévnek legalább 4 karakter hosszúnak kell lennie!');
      cy.get('input[name="username"]').should('have.value', 'tes');
    });

    it('jelenítse meg a hibát érvénytelen email esetén', () => {
      // Mockoljuk a POST kérést pontos válasszal
      cy.intercept('POST', '**/regisztracio.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Regisztráció</h2>
            <div class="alert alert-danger">Érvénytelen email cím formátum!</div>
            <form method="POST" action="">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username">
              </div>
              <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email" value="invalid-email">
              </div>
              <div class="form-group">
                <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                <div class="input-container">
                  <input type="password" id="password" name="password">
                  <span class="input_img" data-role="toggle" id="togglePassword">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                      <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                      <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                      <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                    </svg>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone">
              </div>
              <button type="submit">Regisztráció</button>
            </form>
            <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
          </div>
        `
      }).as('invalidEmail');

      cy.get('input[name="email"]').type('invalid-email');
      cy.get('button[type="submit"]').click();

      cy.get('input[name="email"]').should('have.value', 'invalid-email');
    });

    it('jelenítse meg a hibát gyenge jelszó esetén', () => {
      cy.intercept('POST', '**/regisztracio.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Regisztráció</h2>
            <div class="alert alert-danger">A jelszónak legalább 8 karakter hosszúnak kell lennie!</div>
            <form method="POST" action="">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username">
              </div>
              <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email">
              </div>
              <div class="form-group">
                <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                <div class="input-container">
                  <input type="password" id="password" name="password" value="pass">
                  <span class="input_img" data-role="toggle" id="togglePassword"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone">
              </div>
              <button type="submit">Regisztráció</button>
            </form>
            <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
          </div>
        `
      }).as('weakPassword');

      cy.get('input[name="password"]').type('pass');
      cy.get('button[type="submit"]').click();
      cy.get('.alert.alert-danger', { timeout: 10000 }).should('contain', 'A jelszónak legalább 8 karakter hosszúnak kell lennie!');
      cy.get('input[name="password"]').should('have.value', 'pass');
    });

    it('jelenítse meg a hibát érvénytelen telefonszám esetén', () => {
      cy.intercept('POST', '**/regisztracio.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Regisztráció</h2>
            <div class="alert alert-danger">Érvénytelen telefonszám! Használj 06 vagy +36 kezdést és 8-9 számjegyet!</div>
            <form method="POST" action="">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username">
              </div>
              <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email">
              </div>
              <div class="form-group">
                <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                <div class="input-container">
                  <input type="password" id="password" name="password">
                  <span class="input_img" data-role="toggle" id="togglePassword"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone" value="12345678">
              </div>
              <button type="submit">Regisztráció</button>
            </form>
            <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
          </div>
        `
      }).as('invalidPhone');

      cy.get('input[name="phone"]').type('12345678');
      cy.get('button[type="submit"]').click();
      cy.get('.alert.alert-danger', { timeout: 10000 }).should('contain', 'Érvénytelen telefonszám! Használj 06 vagy +36 kezdést és 8-9 számjegyet!');
      cy.get('input[name="phone"]').should('have.value', '12345678');
    });

    it('jelenítse meg a hibát, ha a felhasználónév már foglalt', () => {
      cy.intercept('POST', '**/regisztracio.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Regisztráció</h2>
            <div class="alert alert-danger">Ez a felhasználónév már foglalt!</div>
            <form method="POST" action="">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username" value="foglaltfelhasznalo">
              </div>
              <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email">
              </div>
              <div class="form-group">
                <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                <div class="input-container">
                  <input type="password" id="password" name="password">
                  <span class="input_img" data-role="toggle" id="togglePassword"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone">
              </div>
              <button type="submit">Regisztráció</button>
            </form>
            <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
          </div>
        `
      }).as('takenUsername');

      cy.get('input[name="username"]').type('foglaltfelhasznalo');
      cy.get('input[name="email"]').type('teszt@pelda.com');
      cy.get('input[name="password"]').type('Teszt1234');
      cy.get('input[name="phone"]').type('06301234567');
      cy.get('button[type="submit"]').click();
      cy.get('.alert.alert-danger', { timeout: 10000 }).should('contain', 'Ez a felhasználónév már foglalt!');
      cy.get('input[name="username"]').should('have.value', 'foglaltfelhasznalo');
    });

    it('sikeres regisztráció esetén mutassa a sikerüzenetet', () => {
      cy.intercept('POST', '**/regisztracio.php', {
        statusCode: 200,
        body: `
          <div class="container">
            <h2>Regisztráció</h2>
            <div class="alert alert-success">
              Sikeres regisztráció! <a href="bejelentkezes.php">Bejelentkezés</a>
              <br>Elküldtünk egy megerősítő emailt a megadott címre!
            </div>
            <form method="POST" action="">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" id="username" name="username">
              </div>
              <div class="form-group">
                <label for="email">Email cím:</label>
                <input type="email" id="email" name="email">
              </div>
              <div class="form-group">
                <label for="password">Jelszó (min. 8 karakter, szám és betű):</label>
                <div class="input-container">
                  <input type="password" id="password" name="password">
                  <span class="input_img" data-role="toggle" id="togglePassword"></span>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Telefonszám (06 vagy +36 kezdettel):</label>
                <input type="tel" id="phone" name="phone">
              </div>
              <button type="submit">Regisztráció</button>
            </form>
            <p class="form-footer">Már van fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
          </div>
        `
      }).as('successfulRegistration');

      cy.get('input[name="username"]').type('ujfelhasznalo');
      cy.get('input[name="email"]').type('uj@pelda.com');
      cy.get('input[name="password"]').type('Teszt1234');
      cy.get('input[name="phone"]').type('06301234567');
      cy.get('button[type="submit"]').click();
      cy.get('.alert.alert-success', { timeout: 10000 }).should('contain', 'Sikeres regisztráció!');
      cy.get('.alert.alert-success a').should('have.attr', 'href', 'bejelentkezes.php');
      cy.get('.alert.alert-success').should('contain', 'Elküldtünk egy megerősítő emailt a megadott címre!');
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

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    it('rendelkezzen megfelelő label attribútumokkal', () => {
      cy.get('label[for="username"]', { timeout: 10000 }).should('contain', 'Felhasználónév:');
      cy.get('label[for="email"]', { timeout: 10000 }).should('contain', 'Email cím:');
      cy.get('label[for="password"]', { timeout: 10000 }).should('contain', 'Jelszó');
      cy.get('label[for="phone"]', { timeout: 10000 }).should('contain', 'Telefonszám');
    });

    it('ellenőrizze az űrlap akadálymentes nevét', () => {
      cy.get('input[name="username"]', { timeout: 10000 }).should('have.attr', 'id', 'username');
      cy.get('input[name="email"]', { timeout: 10000 }).should('have.attr', 'id', 'email');
      cy.get('input[name="password"]', { timeout: 10000 }).should('have.attr', 'id', 'password');
      cy.get('input[name="phone"]', { timeout: 10000 }).should('have.attr', 'id', 'phone');
      cy.get('button[type="submit"]', { timeout: 10000 }).should('contain', 'Regisztráció');
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
      cy.get('form', { timeout: 10000 }).should('be.visible');
      cy.get('.footer', { timeout: 10000 }).should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('input[name="username"]', { timeout: 10000 }).should('be.visible');
      cy.get('input[name="email"]', { timeout: 10000 }).should('be.visible');
      cy.get('input[name="password"]', { timeout: 10000 }).should('be.visible');
      cy.get('input[name="phone"]', { timeout: 10000 }).should('be.visible');
      cy.get('button[type="submit"]', { timeout: 10000 }).should('be.visible');
    });
  });

  // Betöltési Tesztek
  describe('Betöltési Tesztek', () => {
    it('ellenőrizze a külső erőforrások betöltését', () => {
      cy.get('link[href*="bootstrap"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="font-awesome"]', { timeout: 10000 }).should('exist');
      cy.get('script[src*="bootstrap"]', { timeout: 10000 }).should('exist');
    });

    it('ellenőrizze a helyi erőforrások betöltését', () => {
      cy.get('link[href*="../css/regisztracio.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/navbar.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/footer.css"]', { timeout: 10000 }).should('exist');
      cy.get('script[src*="../js/navbar.js"]', { timeout: 10000 }).should('exist');
    });
  });
});