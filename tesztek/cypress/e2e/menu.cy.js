describe('FlavorWave Kategóriák Oldal Tesztek', () => {
  beforeEach(() => {
    // Látogassa meg a kategóriák oldalt minden teszt előtt
    cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kategoria.php'); // Módosítsd, ha más az URL
  });

  // Navigációs Tesztek
  describe('Navigációs Tesztek', () => {


    it('navigáljon a lábléc linkekre', () => {
      cy.get('.footer-links a').contains('Rólunk').click();
      cy.url().should('include', 'rolunk.html');

      cy.go('back');
      cy.get('.footer-links a').contains('Kapcsolat').click();
      cy.url().should('include', 'kapcsolatok.html');

      cy.go('back');
      cy.get('.footer-links a').contains('Adatvédelem').click();
      cy.url().should('include', 'adatvedelem.html');
    });

    it('nyissa meg a közösségi média linkeket új lapon', () => {
      cy.get('.footer-socials a').each(($el) => {
        cy.wrap($el).should('have.attr', 'target', '_blank');
      });
    });
  });

  // Oldalcím Tesztek
  describe('Oldalcím Tesztek', () => {
    it('mutassa a helyes oldalcímet', () => {
      cy.get('.page-title').contains('Kategóriák').should('be.visible');
    });

    it('ellenőrizze az oldal meta címét', () => {
      cy.title().should('eq', 'FlavorWave - Kategóriák');
    });
  });

  // Kategóriák Rács Tesztek
  describe('Kategóriák Rács Tesztek', () => {
    it('mutassa az összes kategóriát', () => {
      cy.get('.flex-grid .flex-col').should('have.length', 7);
    });

    it('ellenőrizze a kategóriák nevét és linkjeit', () => {
      const categories = [
        { name: 'Hamburgerek', link: 'hamburger.php' },
        { name: 'Pizzák', link: 'pizza.php' },
        { name: 'Hot-dogok', link: 'hotdog.php' },
        { name: 'Köretek', link: 'koretek.php' },
        { name: 'Desszertek', link: 'desszertek.php' },
        { name: 'Shakek', link: 'shakek.php' },
        { name: 'Italok', link: 'italok.php' },
      ];

      cy.get('.flex-grid .flex-col').each(($col, index) => {
        cy.wrap($col)
          .find('h2')
          .should('contain', categories[index].name);
        cy.wrap($col)
          .find('a.menu__option')
          .should('have.attr', 'href', categories[index].link);
      });
    });

    it('ellenőrizze a népszerű címkéket', () => {
      cy.get('.flex-col').eq(0).find('.popular-label').should('contain', 'Népszerű');
      cy.get('.flex-col').eq(1).find('.popular-label').should('contain', 'Népszerű');
      cy.get('.flex-col').eq(2).find('.popular-label').should('not.exist');
    });

    it('ellenőrizze a rendelési gombokat', () => {
      cy.get('.flex-col').each(($col) => {
        cy.wrap($col)
          .find('.order-btn')
          .should('contain', 'Rendelj most')
          
      });
    });

    it('navigáljon a kategória oldalakra a rendelési gombokkal', () => {
      cy.get('.flex-col').eq(0).find('.order-btn').click();
      cy.url().should('include', 'hamburger.php');

      cy.go('back');
      cy.get('.flex-col').eq(1).find('.order-btn').click();
      cy.url().should('include', 'pizza.php');

      cy.go('back');
      cy.get('.flex-col').eq(4).find('.order-btn').click();
      cy.url().should('include', 'desszertek.php');
    });
  });

  // Kép Tesztek
  describe('Kép Tesztek', () => {
    it('ellenőrizze a kategóriák képeit', () => {
      const imageAlts = [
        'Hamburgerek',
        'Pizzák',
        'Hot-dogok',
        'Köretek',
        'Desszertek',
        'Shakek',
        'Italok',
      ];

      cy.get('.flex-grid .image-wrapper img').each(($img, index) => {
        cy.wrap($img).should('have.attr', 'alt', imageAlts[index]);
        
      });
    });

    it('ellenőrizze a képek betöltését', () => {
      cy.get('.flex-grid .image-wrapper img').each(($img) => {
        cy.wrap($img)
          .should('have.prop', 'naturalWidth')
          .and('be.greaterThan', 0);
      });
    });
  });

  // AOS Animáció Tesztek
  describe('AOS Animáció Tesztek', () => {
    it('inicializálja az AOS animációkat', () => {
      cy.window().then((win) => {
        expect(win.AOS).to.exist;
      });
    });

    it('alkalmazza az AOS attribútumokat a kategóriákra', () => {
      cy.get('.flex-col').each(($col, index) => {
        cy.wrap($col)
          .should('have.attr', 'data-aos', 'fade-up')
          .should('have.attr', 'data-aos-delay', `${100 + index * 100}`);
      });
    });
  });

  // Hozzáférhetőségi Tesztek
  describe('Hozzáférhetőségi Tesztek', () => {
    it('rendelkezzen megfelelő alt attribútumokkal a képekhez', () => {
      cy.get('img').each(($img) => {
        cy.wrap($img).should('have.attr', 'alt').and('not.be.empty');
      });
    });

    it('ellenőrizze a linkek akadálymentes nevét', () => {
      cy.get('.menu__option').each(($link) => {
        cy.wrap($link).should('have.attr', 'href').and('not.be.empty');
        cy.wrap($link)
          .find('h2')
          .invoke('text')
          .should('not.be.empty');
      });
    });
  });

  // Reszponzív Dizájn Tesztek
  describe('Reszponzív Dizájn Tesztek', () => {
    it('helyesen jelenjen meg mobilon', () => {
      cy.viewport('iphone-x');
      cy.get('.flex-grid').should('be.visible');
      cy.get('.page-title').should('be.visible');
      cy.get('.flex-col').first().find('.order-btn').should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('.flex-grid').should('be.visible');
      cy.get('.flex-col').should('have.length', 7);
      cy.get('.footer').should('be.visible');
    });

    
  });

  // Betöltési Tesztek
  describe('Betöltési Tesztek', () => {
    it('ellenőrizze a külső erőforrások betöltését', () => {
      cy.get('link[href*="bootstrap"]').should('exist');
      cy.get('link[href*="font-awesome"]').should('exist');
      cy.get('link[href*="aos"]').should('exist');
      cy.get('script[src*="aos"]').should('exist');
    });

    it('ellenőrizze a helyi CSS fájlok betöltését', () => {
      cy.get('link[href*="../css/navbar.css"]').should('exist');
      cy.get('link[href*="../css/footer.css"]').should('exist');
      cy.get('link[href*="../css/kategoriak.css"]').should('exist');
      cy.get('link[href*="../css/fooldal/ujfooldal.css"]').should('exist');
    });
  });
});