/**
 * @jest-environment jsdom
 */

const { updateCartCount } = require('./cart');

// Mockoljuk a fetch hívást
global.fetch = jest.fn(() =>
  Promise.resolve({
    json: () => Promise.resolve({ count: 5 }),
  })
);

describe('Kosár számláló frissítése', () => {
  beforeEach(() => {
    // Minden teszt előtt reseteljük a mockokat
    fetch.mockClear();
    document.body.innerHTML = `
      <span class="cart-count">0</span>
      <span class="cart-count">0</span>
    `;
  });

  test('Sikeresen frissíti a kosár számlálót', async () => {
    await updateCartCount();
    
    const elements = document.querySelectorAll('.cart-count');
    elements.forEach(element => {
      expect(element.textContent).toBe('5');
    });
    
    expect(fetch).toHaveBeenCalledTimes(1);
    expect(fetch).toHaveBeenCalledWith('get_cart_count.php');
  });

  test('Hiba esetén logolja a hibát', async () => {
    fetch.mockImplementationOnce(() => Promise.reject('Hálózati hiba'));
    console.error = jest.fn();
    
    await updateCartCount();
    
    expect(console.error).toHaveBeenCalledWith(
      'Hiba a kosár számláló frissítése során:', 
      'Hálózati hiba'
    );
  });

  test('DOMContentLoaded esemény indítja a frissítést', () => {
    const mockUpdate = jest.fn();
    global.updateCartCount = mockUpdate;
    
    // Manuálisan triggereljük az eseményt
    document.dispatchEvent(new Event('DOMContentLoaded'));
    
    expect(mockUpdate).toHaveBeenCalledTimes(1);
  });
});