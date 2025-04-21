describe('FlavorWave Website Tests', () => {
  beforeEach(() => {
    // Visit the homepage before each test
    cy.visit('http://localhost/vizsgaprojekt/Flavorwave/php/kezdolap.php'); // Adjust URL based on your local server
  });

  // Test Navigation
  describe('Navigációs Tesztek', () => {
    it('navigáljon a menü oldalra az "Ugorj a menüre" kattintásakor', () => {
      cy.get('.view-menu').contains('Ugorj a menüre').click();
      cy.url().should('include', 'kategoria.php');
    });

    it('mutassa a regisztrációs linket be nem jelentkezett felhasználóknak', () => {
      cy.get('.order-now').contains('Regisztrálj most').should('be.visible');
      cy.get('.order-now').click();
      cy.url().should('include', 'regisztracio.php');
    });

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

  // Test Hero Section
  describe('Hero Szekció Tesztek', () => {
    it('mutassa a hero videót és tartalmat', () => {
      cy.get('.hero-video').should('be.visible');
      cy.get('.hero-content h1').contains('Friss, Forró, Finom').should('be.visible');
      cy.get('.hero-content p').contains('Rendelj kedvenc ételeid közül gyorsan és egyszerűen!').should('be.visible');
    });

    it('rendelkezzen működő CTA gombokkal', () => {
      cy.get('.cta-buttons .view-menu').should('be.visible').click();
      cy.url().should('include', 'kategoria.php');
    });
  });

  // Test Coupon Slider
  describe('Kupon Slider Tesztek', () => {
    it('mutassa a kupon slide-okat', () => {
      cy.get('.coupon-slider .slide').should('have.length', 3);
    });

    it('navigáljon a kupon slide-okon nyilakkal', () => {
      cy.get('.coupon-slider .slide').first().should('be.visible');
      cy.get('.coupon-slider .next').click();
      cy.get('.coupon-slider .slide').eq(1).should('be.visible');
      cy.get('.coupon-slider .prev').click();
      cy.get('.coupon-slider .slide').first().should('be.visible');
    });

    it('navigáljon a kupon slide-okon pontokkal', () => {
      cy.get('.dots .dot').eq(1).click();
      cy.get('.coupon-slider .slide').eq(1).should('be.visible');
      cy.get('.dots .dot').eq(2).click();
      cy.get('.coupon-slider .slide').eq(2).should('be.visible');
    });

    it('rendelkezzen működő kupon gombokkal', () => {
      cy.get('.coupon-slider .slide').first().find('.btn').click();
      cy.url().should('include', 'pizza.php');
    });
  });

  // Test Weekly Deals
  describe('Heti Ajánlatok Tesztek', () => {
    it('mutassa a visszaszámláló időzítőt', () => {
      cy.get('#countdown').should('be.visible');
      cy.get('.days').should('exist');
      cy.get('.hours').should('exist');
      cy.get('.minutes').should('exist');
      cy.get('.seconds').should('exist');
    });

    it('mutassa a képslider-eket', () => {
      cy.get('#image-slider-1 img').should('have.length', 3);
      cy.get('#image-slider-2 img').should('have.length', 3);
    });

    it('rendelkezzen működő CTA gombbal', () => {
      cy.get('#weekly-deals .cta-button').click();
      cy.url().should('include', 'kategoria.php');
    });
  });

  // Test Order Steps
  describe('Rendelési Lépések Tesztek', () => {
    it('mutassa az összes rendelési lépést', () => {
      cy.get('.steps-container .step').should('have.length', 4);
      cy.get('.steps-container .step').eq(0).should('contain', 'Válassz ételt');
      cy.get('.steps-container .step').eq(1).should('contain', 'Add meg a címed');
      cy.get('.steps-container .step').eq(2).should('contain', 'Fizess bankkártyával vagy készpénzben');
      cy.get('.steps-container .step').eq(3).should('contain', 'Élvezd az ételt');
    });
  });

  // Test Popular Foods Gallery
  describe('Népszerű Ételek Galéria Tesztek', () => {
    it('mutassa az ételkártyákat', () => {
      cy.get('.food-gallery-grid .food-card').should('have.length', 3);
    });

    it('mutassa a helyes étel részleteket', () => {
      cy.get('.food-card').eq(0).find('h3').should('contain', 'Margherita Pizza');
      cy.get('.food-card').eq(1).find('h3').should('contain', 'Sonkás Pizza');
      cy.get('.food-card').eq(2).find('h3').should('contain', 'Almás Pite');
    });

    it('rendelkezzen működő rendelési gombokkal', () => {
      cy.get('.food-card').eq(0).find('.order-btn').click();
      cy.url().should('include', 'pizza.php');

      cy.go('back');
      cy.get('.food-card').eq(2).find('.order-btn').click();
      cy.url().should('include', 'desszertek.php');
    });

    it('mutassa az értékeléseket', () => {
      cy.get('.food-card').each(($card) => {
        cy.wrap($card).find('.food-rating .star').should('have.length', 5);
      });
    });
  });

  // Test Shaker Slider
  describe('Shake Slider Tesztek', () => {
    it('mutassa a shake slide-okat', () => {
      cy.get('.shaker-slides .shaker-slide').should('have.length', 3);
    });

    it('navigáljon a shake slide-okon nyilakkal', () => {
      cy.get('.shaker-slide').first().should('be.visible');
      cy.get('.shaker-next').click();
      cy.get('.shaker-slide').eq(1).should('be.visible');
      cy.get('.shaker-prev').click();
      cy.get('.shaker-slide').first().should('be.visible');
    });

    it('navigáljon a shake slide-okon pontokkal', () => {
      cy.get('.shaker-dots .shaker-dot').eq(1).click();
      cy.get('.shaker-slide').eq(1).should('be.visible');
      cy.get('.shaker-dots .shaker-dot').eq(2).click();
      cy.get('.shaker-slide').eq(2).should('be.visible');
    });

    it('rendelkezzen működő shake gombokkal', () => {
      cy.get('.shaker-slide').first().find('.shaker-btn').click();
      cy.url().should('include', 'shakek.php');
    });
  });

  // Test Feedback Section
  describe('Visszajelzés Szekció Tesztek', () => {
    it('mutassa a bejelentkezési felszólítást be nem jelentkezett felhasználóknak', () => {
      cy.get('.feedback-section p').contains('Kérjük, jelentkezz be').should('be.visible');
      cy.get('.feedback-section a').contains('jelentkezz be').click();
      cy.url().should('include', 'bejelentkezes.php');
    });
  });

  // Test Why Us Section
  describe('Miért Minket Szekció Tesztek', () => {
    it('mutassa az összes funkciót', () => {
      cy.get('.why-us .features .feature').should('have.length', 3);
      cy.get('.why-us .feature').eq(0).should('contain', 'Villámgyors kiszállítás');
      cy.get('.why-us .feature').eq(1).should('contain', 'Friss alapanyagok');
      cy.get('.why-us .feature').eq(2).should('contain', 'Egyedi ízek');
    });
  });

  // Test AOS Animations
  describe('AOS Animáció Tesztek', () => {
    it('inicializálja az AOS animációkat', () => {
      cy.window().then((win) => {
        expect(win.AOS).to.exist;
      });
    });

    it('alkalmazza az AOS attribútumokat az elemekre', () => {
      cy.get('[data-aos="fade-up"]').should('exist');
    });
  });

  // Test Accessibility
  describe('Hozzáférhetőségi Tesztek', () => {
    it('rendelkezzen megfelelő alt attribútumokkal a képekhez', () => {
      cy.get('img').each(($img) => {
        cy.wrap($img).should('have.attr', 'alt').and('not.be.empty');
      });
    });
  });

  // Test Responsive Design
  describe('Reszponzív Dizájn Tesztek', () => {
    it('helyesen jelenjen meg mobilon', () => {
      cy.viewport('iphone-x');
      cy.get('.hero-content').should('be.visible');
      cy.get('.coupon-slider').should('be.visible');
    });

    it('helyesen jelenjen meg tableten', () => {
      cy.viewport('ipad-2');
      cy.get('.steps-container').should('be.visible');
      cy.get('.food-gallery-grid').should('be.visible');
    });
  });
});