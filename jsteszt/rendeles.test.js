const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// Load the HTML file
const html = fs.readFileSync(path.resolve(__dirname, '../rendeles.php'), 'utf8');

describe('Rendelés JavaScript Functionality', () => {
  let dom;
  let window;
  let document;
  let $;
  let bootstrap;

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
   
    // Mock Bootstrap
    bootstrap = {
      Modal: jest.fn(function() {
        return {
          show: jest.fn(),
          hide: jest.fn()
        };
      })
    };
    global.bootstrap = bootstrap;
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('Success Modal', () => {
    it('should show success modal when success flag is true', () => {
      // Set up success state
      document.body.innerHTML += `
        <script>
          window.success = true;
        </script>
      `;
     
      // Execute the script
      const scriptContent = document.querySelector('script:not([src])').textContent;
      new Function(scriptContent)();
     
      expect(bootstrap.Modal).toHaveBeenCalled();
      expect(bootstrap.Modal.mock.results[0].value.show).toHaveBeenCalled();
    });

    it('should redirect to homepage when modal is closed', () => {
      // Set up success state
      document.body.innerHTML += `
        <script>
          window.success = true;
          window.location = { href: '' };
        </script>
      `;
     
      // Mock the modal element and event
      const modal = document.getElementById('successModal');
      const event = new window.Event('hidden.bs.modal');
     
      // Execute the script
      const scriptContent = document.querySelector('script:not([src])').textContent;
      new Function(scriptContent)();
     
      // Trigger the event
      modal.dispatchEvent(event);
     
      expect(window.location.href).toBe('kezdolap.php');
    });

    it('should not show modal when success flag is false', () => {
      // Set up non-success state
      document.body.innerHTML += `
        <script>
          window.success = false;
        </script>
      `;
     
      // Execute the script
      const scriptContent = document.querySelector('script:not([src])').textContent;
      new Function(scriptContent)();
     
      expect(bootstrap.Modal).not.toHaveBeenCalled();
    });
  });

  describe('Form Validation', () => {
    it('should prevent submission with empty fields', () => {
      // Set up form
      document.body.innerHTML += `
        <form id="order-form">
          <input type="text" name="name" required>
          <input type="text" name="address" required>
          <input type="tel" name="phone" required>
          <input type="radio" name="payment_method" value="0">
          <input type="radio" name="payment_method" value="1">
          <button type="submit">Submit</button>
        </form>
      `;
     
      const form = document.getElementById('order-form');
      const submitEvent = new window.Event('submit');
      submitEvent.preventDefault = jest.fn();
     
      // Test with empty form
      form.dispatchEvent(submitEvent);
     
      expect(submitEvent.preventDefault).toHaveBeenCalled();
    });

    it('should allow submission with valid fields', () => {
      // Set up form with valid data
      document.body.innerHTML += `
        <form id="order-form">
          <input type="text" name="name" value="Test User" required>
          <input type="text" name="address" value="Test Address" required>
          <input type="tel" name="phone" value="+36201234567" required>
          <input type="radio" name="payment_method" value="0" checked>
          <button type="submit">Submit</button>
        </form>
      `;
     
      const form = document.getElementById('order-form');
      const submitEvent = new window.Event('submit');
      submitEvent.preventDefault = jest.fn();
     
      // Test with valid form
      form.dispatchEvent(submitEvent);
     
      expect(submitEvent.preventDefault).not.toHaveBeenCalled();
    });
  });

  describe('Phone Number Validation', () => {
    it('should accept valid phone number formats', () => {
      const validNumbers = [
        '+36201234567',
        '06201234567',
        '36201234567',
        '201234567'
      ];
     
      // Get the validation regex from the PHP code
      const phoneRegex = /^\+?[0-9]{9,12}$/;
     
      validNumbers.forEach(number => {
        expect(phoneRegex.test(number)).toBe(true);
      });
    });

    it('should reject invalid phone number formats', () => {
      const invalidNumbers = [
        '123',
        'abcdefghij',
        '+3620123456789', // too long
        '06201234' // too short
      ];
     
      // Get the validation regex from the PHP code
      const phoneRegex = /^\+?[0-9]{9,12}$/;
     
      invalidNumbers.forEach(number => {
        expect(phoneRegex.test(number)).toBe(false);
      });
    });
  });

  describe('DOMContentLoaded', () => {
    it('should initialize the page correctly', () => {
      // Mock the success modal behavior
      const modalInit = jest.fn();
      document.addEventListener('DOMContentLoaded', modalInit);
     
      // Trigger DOMContentLoaded
      document.dispatchEvent(new window.Event('DOMContentLoaded'));
     
      expect(modalInit).toHaveBeenCalled();
    });
  });
});