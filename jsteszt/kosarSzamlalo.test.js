/**
 * @jest-environment jsdom
 */

const {
  openCustomModal,
  closeCustomModal,
  addToCart,
  showToast,
  animateCartAdd
} = require('./kosarSzamlalo');

// Mock AOS inicializálása
global.AOS = { init: jest.fn() };

describe('Modális ablak kezelése', () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <div id="test-modal" class="custom-modal" style="display:none;"></div>
      <body style="overflow: auto;"></body>
    `;
  });

  test('Modális ablak megnyitása', () => {
    openCustomModal('test-modal');
    const modal = document.getElementById('test-modal');
    
    expect(modal.style.display).toBe('block');
    expect(modal.classList.contains('modal-open')).toBe(true);
    expect(document.body.style.overflow).toBe('hidden');
  });

  test('Modális ablak bezárása animációval', () => {
    jest.useFakeTimers();
    openCustomModal('test-modal');
    closeCustomModal('test-modal');
    
    const modal = document.getElementById('test-modal');
    expect(modal.classList.contains('modal-close')).toBe(true);
    
    // Gyorsan előretekerjük az időt a timeout miatt
    jest.advanceTimersByTime(300);
    
    expect(modal.style.display).toBe('none');
    expect(modal.classList.contains('modal-open')).toBe(false);
    expect(document.body.style.overflow).toBe('auto');
  });
});

describe('Kosárkezelés', () => {
  beforeEach(() => {
    global.fetch = jest.fn(() =>
      Promise.resolve({
        json: () => Promise.resolve({ success: true }),
      })
    );
    
    document.body.innerHTML = `
      <button data-item-id="1" data-image="pizza.jpg"></button>
      <div class="cart-btn"></div>
      <div id="cart-animation-item" style="display:none;"></div>
      <div id="toast-added" style="display:none;">
        <div class="toast-body"></div>
      </div>
    `;
  });

  test('Termék hozzáadása a kosárhoz', async () => {
    await addToCart('1');
    
    expect(fetch).toHaveBeenCalledWith('kosarba_rakas.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ itemId: '1' })
    });
  });

  test('Toast üzenet megjelenítése', () => {
    showToast('Teszt üzenet');
    const toast = document.getElementById('toast-added');
    
    expect(toast.style.display).toBe('block');
    expect(document.querySelector('.toast-body').textContent).toBe('Teszt üzenet');
    
    // Timeout tesztelése
    jest.useFakeTimers();
    jest.advanceTimersByTime(3000);
    expect(toast.style.display).toBe('none');
  });

  test('Kosárba rakás animációja', () => {
    const button = document.querySelector('[data-item-id="1"]');
    animateCartAdd(button, 'pizza.jpg');
    
    const animItem = document.getElementById('cart-animation-item');
    expect(animItem.style.display).toBe('block');
    expect(animItem.style.backgroundImage).toBe('url(pizza.jpg)');
  });
});