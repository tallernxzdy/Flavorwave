/**
 * @jest-environment jsdom
 */

require('./navbar');

describe('Navigációs sáv tesztelése', () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <button class="hamburger"></button>
      <nav class="menubar"></nav>
      <span class="cart-count">0</span>
    `;
  });

  test('Hamburger menü megnyitása és bezárása', () => {
    const hamburger = document.querySelector('.hamburger');
    const menubar = document.querySelector('.menubar');
    
    hamburger.click();
    expect(menubar.classList.contains('active')).toBe(true);
    expect(hamburger.classList.contains('active')).toBe(true);
    
    hamburger.click();
    expect(menubar.classList.contains('active')).toBe(false);
    expect(hamburger.classList.contains('active')).toBe(false);
  });

  test('Menü bezárása nagy képernyőn', () => {
    const hamburger = document.querySelector('.hamburger');
    const menubar = document.querySelector('.menubar');
    
    // Nyitott menüvel kezdünk
    hamburger.click();
    
    // Mockoljuk a nagy képernyőt
    window.innerWidth = 1300;
    window.dispatchEvent(new Event('resize'));
    
    expect(menubar.classList.contains('active')).toBe(false);
    expect(hamburger.classList.contains('active')).toBe(false);
  });

  test('Kosár számláló frissítése', async () => {
    global.fetch = jest.fn(() =>
      Promise.resolve({
        json: () => Promise.resolve({ count: 3 }),
      })
    );
    
    await require('./navbar').updateCartCount();
    
    const counter = document.querySelector('.cart-count');
    expect(counter.textContent).toBe('3');
  });
});