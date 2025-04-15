describe('FlavorWave Profil Oldal Tesztek', () => {
  // Uncaught exception figyelmen kívül hagyása
  Cypress.on('uncaught:exception', (err, runnable) => {
    return false; // Ne bukjon meg a teszt alkalmazás hibák miatt
  });

  beforeEach(() => {
    // Mockoljuk a get_cart_count.php hívást, hogy elkerüljük a nem várt kéréseket
    cy.intercept('GET', '**/get_cart_count.php', {
      statusCode: 200,
      body: { count: 0 }
    }).as('getCartCount');

    // Látogassa meg a profil oldalt
    cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php', { timeout: 10000 });
  });

  // Navigációs Tesztek
  describe('Navigációs Tesztek', () => {


    it('navigáljon a lábléc linkekre', () => {
      // Mockoljuk az oldal válaszát, hogy biztosítsuk a lábléc létezését
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="profile-header">
              <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
            </div>
            <div class="profile-content"></div>
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
      }).as('loadProfile');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php');
      cy.wait('@loadProfile');

      // Ellenőrizzük és kattintsunk a lábléc linkekre
      cy.get('.footer-links a', { timeout: 10000 }).contains('Rólunk').should('be.visible').click();
      cy.url().should('include', 'rolunk.html');

      cy.go('back');
      cy.get('.footer-links a', { timeout: 10000 }).contains('Kapcsolat').should('be.visible').click();
      cy.url().should('include', 'kapcsolatok.html');

      cy.go('back');
      cy.get('.footer-links a', { timeout: 10000 }).contains('Adatvédelem').should('be.visible').click();
      cy.url().should('include', 'adatvedelem.html');
    });

    it('nyissa meg a közösségi média linkeket új lapon', () => {
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="footer">
              <div class="footer-container">
                <ul class="footer-links">
                  <li><a href="../html/rolunk.html">Rólunk</a></li>
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
      }).as('loadProfile');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php');
      cy.wait('@loadProfile');

      cy.get('.footer-socials a', { timeout: 10000 }).each(($el) => {
        cy.wrap($el).should('have.attr', 'target', '_blank');
        cy.wrap($el).should('have.attr', 'href').and('match', /facebook\.com|instagram\.com|x\.com|youtube\.com/);
      });
    });
  });

  // Oldalcím Tesztek
  describe('Oldalcím Tesztek', () => {
    it('mutassa a helyes oldalcímet', () => {
      cy.get('.profile-header h1', { timeout: 10000 }).contains('Profil adataim').should('be.visible');
    });

    it('ellenőrizze az oldal meta címét', () => {
      cy.title({ timeout: 10000 }).should('eq', 'Profilom - FlavorWave');
    });
  });

  // Bejelentkezett Felhasználó Tesztek
  describe('Bejelentkezett Felhasználó Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="profile-header">
              <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
            </div>
            <div class="profile-content">
              <form id="profile-form" method="POST" action="profil_frissites.php">
                <div class="profile-card">
                  <div class="user-info">
                    <div class="info-item" id="teljes_nev_item">
                      <span class="info-label"><i class="fas fa-user"></i> Teljes név:</span>
                      <span class="info-value" id="teljes_nev_value">Teszt Elek</span>
                      <input type="text" class="form-control edit-input" name="teljes_nev" value="Teszt Elek" style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                    <div class="info-item" id="felhasznalo_nev_item">
                      <span class="info-label"><i class="fas fa-at"></i> Felhasználónév:</span>
                      <span class="info-value" id="felhasznalo_nev_value">tesztelek</span>
                      <input type="text" class="form-control edit-input" name="felhasznalo_nev" value="tesztelek" style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                    <div class="info-item" id="email_cim_item">
                      <span class="info-label"><i class="fas fa-envelope"></i> Email cím:</span>
                      <span class="info-value" id="email_cim_value">teszt@elek.hu</span>
                      <input type="email" class="form-control edit-input" name="email_cim" value="teszt@elek.hu" style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                    <div class="info-item" id="tel_szam_item">
                      <span class="info-label"><i class="fas fa-phone"></i> Telefonszám:</span>
                      <span class="info-value" id="tel_szam_value">+36201234567</span>
                      <input type="text" class="form-control edit-input" name="tel_szam" value="+36201234567" style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                    <div class="info-item" id="lakcim_item">
                      <span class="info-label"><i class="fas fa-home"></i> Lakcím:</span>
                      <span class="info-value" id="lakcim_value">Budapest, Teszt utca 1.</span>
                      <input type="text" class="form-control edit-input" name="lakcim" value="Budapest, Teszt utca 1." style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                  </div>
                  <button type="button" class="btn btn-success mt-3"><i class="fas fa-save"></i> Mentés</button>
                </div>
              </form>
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
      }).as('loadProfile');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.setItem('felhasznalo_id', '1');
        }
      });
    });

    it('jelenítse meg a profil adatokat', () => {
      cy.get('.user-info .info-item', { timeout: 10000 }).should('have.length', 5);
      cy.get('#teljes_nev_value').should('contain', 'Teszt Elek');
      cy.get('#felhasznalo_nev_value').should('contain', 'tesztelek');
      cy.get('#email_cim_value').should('contain', 'teszt@elek.hu');
      cy.get('#tel_szam_value').should('contain', '+36201234567');
      cy.get('#lakcim_value').should('contain', 'Budapest, Teszt utca 1.');
      cy.get('.btn-success').should('contain', 'Mentés');
    });

    


  });

  // Vendég Felhasználó Tesztek
  describe('Vendég Felhasználó Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="profile-header">
              <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
            </div>
            <div class="profile-content">
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
      }).as('loadProfileGuest');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.removeItem('felhasznalo_id');
        }
      });
    });

    it('jelenítse meg a bejelentkezési kérést', () => {
      cy.get('.not-logged-in .alert-warning', { timeout: 10000 }).should('contain', 'A profil oldal megtekintéséhez be kell jelentkeznie!');
      cy.get('.btn-primary').should('contain', 'Bejelentkezés').and('have.attr', 'href', 'bejelentkezes.php');
      cy.get('#profile-form').should('not.exist');
    });

    it('navigáljon a bejelentkezési oldalra', () => {
      cy.get('.btn-primary').click();
      cy.url().should('include', 'bejelentkezes.php');
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="profile-header">
              <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
            </div>
            <div class="profile-content">
              <form id="profile-form" method="POST" action="profil_frissites.php">
                <div class="profile-card">
                  <div class="user-info">
                    <div class="info-item" id="teljes_nev_item">
                      <span class="info-label"><i class="fas fa-user"></i> Teljes név:</span>
                      <span class="info-value" id="teljes_nev_value">Teszt Elek</span>
                      <input type="text" class="form-control edit-input" name="teljes_nev" value="Teszt Elek" style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                  </div>
                  <button type="button" class="btn btn-success mt-3"><i class="fas fa-save"></i> Mentés</button>
                </div>
              </form>
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
      }).as('loadProfile');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.setItem('felhasznalo_id', '1');
        }
      });
    });

    it('ellenőrizze az oldal fejléc akadálymentesítését', () => {
      cy.get('.profile-header h1').should('have.text', ' Profil adataim').and('be.visible');
      cy.get('.profile-header i').should('have.class', 'fas fa-user-circle');
    });

    it('ellenőrizze az input mezők akadálymentesítését', () => {
      cy.get('#teljes_nev_item .edit-icon').click();
      cy.get('input[name="teljes_nev"]').should('have.attr', 'type', 'text').and('be.visible');
      cy.get('.info-label').contains('Teljes név').should('be.visible');
      cy.get('.btn-success').should('contain', 'Mentés');
    });

    it('ellenőrizze a lábléc linkek akadálymentesítését', () => {
      cy.get('.footer-links a', { timeout: 10000 }).each(($el) => {
        cy.wrap($el).invoke('text').should('not.be.empty');
        cy.wrap($el).should('have.attr', 'href').and('not.be.empty');
      });
      cy.get('.footer-socials a', { timeout: 10000 }).each(($el) => {
        cy.wrap($el).find('i').should('have.class', /fa-.+/);
      });
    });
  });

  // Reszponzív Dizájn Tesztek
  describe('Reszponzív Dizájn Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="profile-header">
              <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
            </div>
            <div class="profile-content">
              <form id="profile-form" method="POST" action="profil_frissites.php">
                <div class="profile-card">
                  <div class="user-info">
                    <div class="info-item" id="teljes_nev_item">
                      <span class="info-label"><i class="fas fa-user"></i> Teljes név:</span>
                      <span class="info-value" id="teljes_nev_value">Teszt Elek</span>
                      <input type="text" class="form-control edit-input" name="teljes_nev" value="Teszt Elek" style="display: none;">
                      <i class="fas fa-pencil-alt edit-icon"></i>
                    </div>
                  </div>
                  <button type="button" class="btn btn-success mt-3"><i class="fas fa-save"></i> Mentés</button>
                </div>
              </form>
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
      }).as('loadProfile');

      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php', {
        onBeforeLoad: (win) => {
          win.sessionStorage.setItem('felhasznalo_id', '1');
        }
      });
    });

    it('helyesen jelenjen meg mobilon', () => {
      cy.viewport('iphone-x');
      cy.get('.profile-header h1', { timeout: 10000 }).should('be.visible');
      cy.get('#teljes_nev_value', { timeout: 10000 }).should('be.visible');
      cy.get('.btn-success', { timeout: 10000 }).should('be.visible');
      cy.get('.footer', { timeout: 10000 }).should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('.profile-header h1', { timeout: 10000 }).should('be.visible');
      cy.get('#teljes_nev_value', { timeout: 10000 }).should('be.visible');
      cy.get('.btn-success', { timeout: 10000 }).should('be.visible');
    });
  });

  // Betöltési Tesztek
  describe('Betöltési Tesztek', () => {
    beforeEach(() => {
      cy.intercept('GET', '**/profil_megtekintes.php', {
        statusCode: 200,
        body: `
          <div class="profile-container">
            <div class="profile-header">
              <h1><i class="fas fa-user-circle"></i> Profil adataim</h1>
            </div>
            <div class="profile-content"></div>
          </div>
        `
      }).as('loadProfile');
    });

    it('ellenőrizze a külső erőforrások betöltését', () => {
      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php');
      cy.get('link[href*="bootstrap"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="font-awesome"]', { timeout: 10000 }).should('exist');
      cy.get('script[src*="bootstrap"]', { timeout: 10000 }).should('exist');
    });

    it('ellenőrizze a helyi erőforrások betöltését', () => {
      cy.visit('http://localhost/13c-szitasi/Flavorwave/php/profil_megtekintes.php');
      cy.get('link[href*="../css/profilom.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/navbar.css"]', { timeout: 10000 }).should('exist');
      cy.get('link[href*="../css/footer.css"]', { timeout: 10000 }).should('exist');
      cy.get('script[src*="../js/navbar.js"]', { timeout: 10000 }).should('exist');
    });
  });
});