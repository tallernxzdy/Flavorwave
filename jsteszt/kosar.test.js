const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// Load the HTML file
const html = fs.readFileSync(path.resolve(__dirname, '../kosar.php'), 'utf8');

// Mock global fetch
global.fetch = jest.fn();

describe('Kosár JavaScript Functionality', () => {
  let dom;
  let window;
  let document;
  let $;

  beforeEach(() => {
    // Create a DOM environment
    dom = new JSDOM(html, {
      runScripts: 'dangerously',
      resources: 'usable'
    });
    window = dom.window;
    document = window.document;
    $ = require('jquery')(window);
   
    // Mock global objects
    global.document = document;
    global.window = window;
   
    // Reset fetch mocks
    fetch.mockReset();
   
    // Mock successful fetch responses
    fetch.mockImplementation((url, options) => {
      if (url === 'kosar_mod.php') {
        return Promise.resolve({
          json: () => Promise.resolve({ success: true, newQuantity: 2 })
        });
      }
      if (url === 'kosarbol_torles.php') {
        return Promise.resolve({
          json: () => Promise.resolve({ success: true })
        });
      }
      if (url === 'kosar_uritese.php') {
        return Promise.resolve({
          json: () => Promise.resolve({ success: true })
        });
      }
      if (url === 'get_cart_count.php') {
        return Promise.resolve({
          json: () => Promise.resolve({ count: 3 })
        });
      }
      return Promise.reject(new Error('Unknown URL'));
    });
   
    // Add some cart items to the DOM for testing
    document.body.innerHTML += `
      <div class="cart-item" data-item-id="1">
        <div class="item-details">
          <img src="image1.jpg">
          <div class="item-info">
            <span class="item-name">Item 1</span>
            <span class="item-price" data-price="1000">Ár: 1000 Ft</span>
          </div>
        </div>
        <div class="quantity-controls">
          <button class="quantity-btn" onclick="updateQuantity(1, 'decrease')">-</button>
          <span class="quantity">1</span>
          <button class="quantity-btn" onclick="updateQuantity(1, 'increase')">+</button>
        </div>
        <span class="item-total">1000 Ft</span>
        <button class="remove-btn" onclick="removeItem(1)">Törlés</button>
      </div>
      <div class="total-section">
        <span class="total-label">Végösszeg:</span>
        <span class="total-amount">1000 Ft</span>
      </div>
      <span class="cart-count">1</span>
    `;
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('updateTotal()', () => {
    it('should calculate and update the total correctly', () => {
      // Get the function from the script
      const updateTotal = new Function(`
        ${document.querySelector('script').textContent}
        return updateTotal;
      `)();
     
      // Add another item to the DOM
      document.body.innerHTML += `
        <div class="cart-item" data-item-id="2">
          <div class="item-details">
            <span class="item-price" data-price="500">Ár: 500 Ft</span>
          </div>
          <div class="quantity-controls">
            <span class="quantity">2</span>
          </div>
          <span class="item-total">1000 Ft</span>
        </div>
      `;
     
      updateTotal();
     
      expect(document.querySelector('.total-amount').textContent).toBe('2000 Ft');
    });
  });

  describe('updateQuantity()', () => {
    it('should increase quantity and update totals', async () => {
      const updateQuantity = new Function(`
        ${document.querySelector('script').textContent}
        return updateQuantity;
      `)();
     
      await updateQuantity(1, 'increase');
     
      expect(fetch).toHaveBeenCalledWith('kosar_mod.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          itemId: 1,
          action: 'increase'
        })
      });
     
      // Check if quantity was updated
      expect(document.querySelector('.cart-item[data-item-id="1"] .quantity').textContent).toBe('2');
      // Check if item total was updated
      expect(document.querySelector('.cart-item[data-item-id="1"] .item-total').textContent).toBe('2000 Ft');
      // Check if cart total was updated
      expect(document.querySelector('.total-amount').textContent).toBe('2000 Ft');
    });

    it('should decrease quantity and remove item if quantity reaches 0', async () => {
      // Mock fetch to return 0 quantity
      fetch.mockImplementationOnce(() =>
        Promise.resolve({
          json: () => Promise.resolve({ success: true, newQuantity: 0 })
        })
      );
     
      const updateQuantity = new Function(`
        ${document.querySelector('script').textContent}
        return updateQuantity;
      `)();
     
      await updateQuantity(1, 'decrease');
     
      // Check if item was removed
      expect(document.querySelector('.cart-item[data-item-id="1"]')).toBeNull();
    });
  });

  describe('removeItem()', () => {
    it('should remove item from cart and update totals', async () => {
      const removeItem = new Function(`
        ${document.querySelector('script').textContent}
        return removeItem;
      `)();
     
      await removeItem(1);
     
      expect(fetch).toHaveBeenCalledWith('kosarbol_torles.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          itemId: 1
        })
      });
     
      // Check if item was removed
      expect(document.querySelector('.cart-item[data-item-id="1"]')).toBeNull();
      // Check if total was updated
      expect(document.querySelector('.total-amount').textContent).toBe('0 Ft');
    });
  });

  describe('updateCartCount()', () => {
    it('should fetch and update cart count', async () => {
      const updateCartCount = new Function(`
        ${document.querySelector('script').textContent}
        return updateCartCount;
      `)();
     
      await updateCartCount();
     
      expect(fetch).toHaveBeenCalledWith('get_cart_count.php', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        }
      });
     
      // Check if cart count was updated
      expect(document.querySelector('.cart-count').textContent).toBe('3');
    });
  });

  describe('checkIfCartEmpty()', () => {
    it('should show empty cart message when cart is empty', () => {
      const checkIfCartEmpty = new Function(`
        ${document.querySelector('script').textContent}
        return checkIfCartEmpty;
      `)();
     
      // Remove all cart items
      document.querySelectorAll('.cart-item').forEach(el => el.remove());
     
      checkIfCartEmpty();
     
      // Check if empty message is shown
      expect(document.querySelector('.checkout-section .error')).not.toBeNull();
      // Check if clear cart button is hidden
      expect(document.getElementById('clearCartBtn').style.display).toBe('none');
    });

    it('should show checkout button when cart has items and user is logged in', () => {
      const checkIfCartEmpty = new Function(`
        ${document.querySelector('script').textContent}
        return checkIfCartEmpty;
      `)();
     
      // Simulate logged in user
      document.body.innerHTML += `
        <script>
          window.userId = 1;
        </script>
      `;
     
      checkIfCartEmpty();
     
      // Check if checkout button is shown
      expect(document.querySelector('.checkout-section form')).not.toBeNull();
    });
  });

  describe('clearCart()', () => {
    it('should clear all items from cart', async () => {
      const clearCart = new Function(`
        ${document.querySelector('script').textContent}
        return clearCart;
      `)();
     
      await clearCart();
     
      expect(fetch).toHaveBeenCalledWith('kosar_uritese.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        }
      });
     
      // Check if items are removed with animation
      expect(document.querySelector('.cart-item').style.opacity).toBe('0');
    });
  });

  describe('DOMContentLoaded', () => {
    it('should initialize cart functions when DOM is loaded', () => {
      // Mock the functions
      const mockCheckIfCartEmpty = jest.fn();
      const mockUpdateCartCount = jest.fn();
     
      // Replace the functions in the script
      const scriptContent = document.querySelector('script').textContent
        .replace('checkIfCartEmpty();', 'mockCheckIfCartEmpty();')
        .replace('updateCartCount();', 'mockUpdateCartCount();');
     
      // Execute the modified script
      new Function(`
        window.mockCheckIfCartEmpty = ${mockCheckIfCartEmpty.toString()};
        window.mockUpdateCartCount = ${mockUpdateCartCount.toString()};
        ${scriptContent}
      `)();
     
      // Trigger DOMContentLoaded
      document.dispatchEvent(new window.Event('DOMContentLoaded'));
     
      expect(mockCheckIfCartEmpty).toHaveBeenCalled();
      expect(mockUpdateCartCount).toHaveBeenCalled();
    });
  });
});